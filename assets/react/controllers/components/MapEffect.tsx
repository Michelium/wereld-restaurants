import {useContext, useEffect} from 'react';
import {useMap} from 'react-leaflet';
import {CountryType} from '../types/CountryType';
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";

interface MapEffectProps {
    selectedCountries: CountryType[];
    onRestaurantsUpdate: (restaurants: any[]) => void;
}

const MapEffect = ({selectedCountries, onRestaurantsUpdate}: MapEffectProps) => {
    const {mapState, setMapState} = useContext(MapContext);
    const map = useMap();

    if (map.getZoom() < 8) {
        setMapState(MapStateRepository.updaters.setRestaurants([])(mapState));
        return;
    }

    useEffect(() => {
        const load = async () => {
            if (map.getZoom() < 8) {
                onRestaurantsUpdate([]);
                return;
            }

            const bounds = map.getBounds();
            const payload = {
                south: bounds.getSouth(),
                north: bounds.getNorth(),
                west: bounds.getWest(),
                east: bounds.getEast(),
            };

            const params = new URLSearchParams();
            params.append('bounds', JSON.stringify(payload));
            selectedCountries.forEach((c) => params.append('countries[]', c.code));

            const res = await fetch(`/api/restaurants?${params.toString()}`);
            const data = await res.json();
            onRestaurantsUpdate(data);
        };

        const handleMoveEnd = () => {
            clearTimeout((map as any)._markerFetchTimeout);
            (map as any)._markerFetchTimeout = setTimeout(() => {
                load();
            }, 300);
        };

        map.on('moveend', handleMoveEnd);
        return () => {
            map.off('moveend', handleMoveEnd);
            clearTimeout((map as any)._markerFetchTimeout);
        };
    }, [map, selectedCountries, onRestaurantsUpdate]);


    return null;
};

export default MapEffect;
