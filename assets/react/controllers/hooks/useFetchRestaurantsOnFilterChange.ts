import {useContext, useEffect} from "react";
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";
import {RestaurantType} from "../types/RestaurantType";
import {CountryType} from "../types/CountryType";

export const useFetchRestaurantsOnFilterChange = () => {
    const {mapState, setMapState} = useContext(MapContext);

    useEffect(() => {
        (async () => {
            try {
                setMapState(MapStateRepository.updaters.setLoading(true));
                const params = new URLSearchParams();
                mapState.filters.countries.forEach((country: CountryType) => {
                    params.append("countries[]", country.code);
                });

                const res = await fetch(`/api/restaurants?${params.toString()}`);
                const data: RestaurantType[] = await res.json();

                setMapState(prev =>
                    MapStateRepository.updaters.setRestaurants(data)(prev)
                );
            } catch (e) {
                console.error(e);
            } finally {
                setMapState(MapStateRepository.updaters.setLoading(false));
            }
        })();
    }, [mapState.filters]);

};
