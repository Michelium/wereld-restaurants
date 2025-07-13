import {useContext, useEffect} from "react";
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";
import {RestaurantType} from "../types/RestaurantType";

export const useFetchRestaurantsOnFilterChange = () => {
    const {mapState, setMapState} = useContext(MapContext);

    useEffect(() => {
        const params = new URLSearchParams();

        mapState.filters.countries.forEach(country => params.append('countries[]', country.code));

        fetch(`/api/restaurants?${params.toString()}`)
            .then(res => res.json())
            .then((data: RestaurantType[]) => {
                setMapState(MapStateRepository.updaters.setRestaurants(data)(mapState));
            })
            .catch(console.error);

    }, [mapState.filters]);
}
