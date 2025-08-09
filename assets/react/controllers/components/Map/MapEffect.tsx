import {useContext, useEffect, useState} from "react";
import {useMap} from "react-leaflet";
import {MapContext, MapStateRepository} from "../../providers/MapContextProvider";
import {CountryType} from "../../types/CountryType";

const MapEffect = () => {
    const {mapState, setMapState} = useContext(MapContext);
    const map = useMap();
    const [shouldShowPrompt, setShouldShowPrompt] = useState(false);

    const hasFilters = mapState.filters.countries.length > 0;

    const fetchRestaurants = async () => {
        const zoom = map.getZoom();
        const skip = zoom < 10 && !hasFilters;

        if (skip) {
            setMapState(MapStateRepository.updaters.setRestaurants([]));
            setShouldShowPrompt(true);
            return;
        }

        setShouldShowPrompt(false);
        setMapState(MapStateRepository.updaters.setLoading(true));

        try {
            const bounds = map.getBounds();
            const payload = {
                south: bounds.getSouth(),
                north: bounds.getNorth(),
                west: bounds.getWest(),
                east: bounds.getEast(),
            };

            const params = new URLSearchParams();
            params.append("bounds", JSON.stringify(payload));
            mapState.filters.countries.forEach((country: CountryType) => {
                params.append("countries[]", country.code);
            });

            const res = await fetch(`/api/restaurants?${params.toString()}`);
            const data = await res.json();

            setMapState(MapStateRepository.updaters.setRestaurants(data));
        } catch (e) {
            console.error(e);
        } finally {
            setMapState(MapStateRepository.updaters.setLoading(false));
        }
    };

    useEffect(() => {
        const handler = () => {
            clearTimeout((map as any)._markerFetchTimeout);
            (map as any)._markerFetchTimeout = setTimeout(() => {
                fetchRestaurants();
            }, 300);
        };

        map.on("moveend", handler);
        return () => {
            map.off("moveend", handler);
            clearTimeout((map as any)._markerFetchTimeout);
        };
    }, [map, mapState.filters]);

    useEffect(() => {
        fetchRestaurants();
    }, [mapState.filters]);

    // Save this flag in state so the map component can show the message
    useEffect(() => {
        setMapState(MapStateRepository.updaters.setShouldShowZoomPrompt(shouldShowPrompt));
    }, [shouldShowPrompt]);

    return null;
};

export default MapEffect;
