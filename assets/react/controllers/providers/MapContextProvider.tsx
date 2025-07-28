import {createContext, Dispatch, ReactNode, SetStateAction, useState} from "react";
import {CountryType} from "../types/CountryType";
import {RestaurantType} from "../types/RestaurantType";

type MapState = {
    restaurants: RestaurantType[];
    filters: {
        countries: CountryType[];
    },
    activeRestaurant: RestaurantType | null;
    loading: boolean;
    shouldShowZoomPrompt: boolean;
}

export const MapStateRepository = {
    default: (): MapState => ({
        restaurants: [],
        filters: {
            countries: []
        },
        activeRestaurant: null,
        loading: false,
        shouldShowZoomPrompt: true
    }),
    updaters: {
        setLoading: (loading: boolean) => (state: MapState): MapState => ({
            ...state,
            loading
        }),
        setRestaurants: (restaurants: RestaurantType[]) => (state: MapState): MapState => ({
            ...state,
            restaurants
        }),
        setFilters: (filters: MapState['filters']) => (state: MapState): MapState => ({
            ...state,
            filters
        }),
        setShouldShowZoomPrompt: (shouldShow: boolean) => (state: MapState): MapState => ({
            ...state,
            shouldShowZoomPrompt: shouldShow
        }),
        setFilterCountries: (countries: CountryType[]) => (state: MapState): MapState => ({
            ...state,
            filters: {
                ...state.filters,
                countries: countries
            }
        }),
        setActiveRestaurant: (restaurant: RestaurantType | null) => (state: MapState): MapState => ({
            ...state,
            activeRestaurant: restaurant
        }),
        clearActiveRestaurant: () => (state: MapState): MapState => ({
            ...state,
            activeRestaurant: null
        }),
    }
}

interface MapContextType {
    mapState: MapState;
    setMapState: Dispatch<SetStateAction<MapState>>;
}

export const MapContext = createContext<MapContextType>({
    mapState: MapStateRepository.default(),
    setMapState: () => {
    }
});

export const MapProvider = ({children}: { children: ReactNode }) => {
    const [mapState, setMapState] = useState<MapState>(MapStateRepository.default());

    return (
        <MapContext.Provider value={{mapState, setMapState}}>
            {children}
        </MapContext.Provider>
    )
}
