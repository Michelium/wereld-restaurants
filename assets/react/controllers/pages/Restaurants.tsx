import React, {useContext} from 'react';
import RestaurantMap from "../components/RestaurantMap";
import RestaurantFilterPanel from "../components/RestaurantFilterPanel";
import '../../../styles/pages/Restaurants.scss';
import {MapContext} from "../providers/MapContextProvider";
import {useFetchRestaurantsOnFilterChange} from "../hooks/useFetchRestaurantsOnFilterChange";
import RestaurantInfoPanel from "../components/RestaurantInfoPanel";

const Restaurants = () => {
    const {mapState} = useContext(MapContext);

    useFetchRestaurantsOnFilterChange();

    return (
        <div className="restaurants-map-wrapper">
            <div className="restaurants-map-wrapper__overlay restaurants-map-wrapper__overlay--filters">
                <RestaurantFilterPanel/>
            </div>
            {mapState.activeRestaurant && (
                <div className="restaurants-map-wrapper__overlay restaurants-map-wrapper__overlay--info">
                    <RestaurantInfoPanel/>
                </div>
            )}
            <div className="restaurants-map">
                <RestaurantMap/>
            </div>
        </div>
    );
};

export default Restaurants;
