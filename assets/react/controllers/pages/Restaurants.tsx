import React, {useContext} from 'react';
import RestaurantMap from "../components/RestaurantMap";
import RestaurantFilterPanel from "../components/RestaurantFilterPanel";
import '../../../styles/pages/Restaurants.scss';
import {MapContext} from "../providers/MapContextProvider";
import RestaurantInfoPanel from "../components/RestaurantInfoPanel";
import ZoomPrompt from "../components/ZoomPrompt";

const Restaurants = () => {
    const {mapState} = useContext(MapContext);

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
                {mapState.shouldShowZoomPrompt && <ZoomPrompt/>}

                <RestaurantMap/>
            </div>
        </div>
    );
};

export default Restaurants;
