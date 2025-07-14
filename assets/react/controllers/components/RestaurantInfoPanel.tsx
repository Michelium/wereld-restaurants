import React, {useContext} from 'react';
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";
import {getCountryIconUrl} from "../utils/getCountryIcon";
import '../../../styles/components/RestaurantInfoPanel.scss';
import {Button} from "@mui/joy";

const RestaurantInfoPanel = () => {
    const {mapState, setMapState} = useContext(MapContext);
    const restaurant = mapState.activeRestaurant;

    if (!restaurant) {
        return;
    }

    return (
        <div className="restaurant-info">
            <div className="restaurant-info__header">
                <strong className="restaurant-info__title">{restaurant.name}</strong>
                {restaurant.country && (
                    <img
                        src={getCountryIconUrl(restaurant.country.code)}
                        alt={restaurant.country.code}
                        className="restaurant-info__flag"
                    />
                )}
            </div>

            <div className="restaurant-info__address">
                {restaurant.street && restaurant.houseNumber && restaurant.postalCode && restaurant.city ? (
                    <>
                        <p>
                            {restaurant.street} {restaurant.houseNumber}<br/>
                            {restaurant.postalCode} {restaurant.city}
                        </p>

                        <a
                            href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(restaurant.name + ' ' + restaurant.street + ' ' + restaurant.houseNumber + ', ' + restaurant.postalCode + ' ' + restaurant.city)}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="restaurant-info__address-link"
                        >
                            Google Maps
                        </a>
                    </>
                ) : 'Adres onbekend'}
            </div>

            <div className="restaurant-info__country">
                {restaurant.country?.name ?? 'Onbekend land'}
            </div>

            <hr/>

            <div className="restaurant-info__actions">
                <Button
                    size="sm"
                    variant="solid"
                    color="primary"
                >
                    Probleem melden
                </Button>
                <Button
                    onClick={() => setMapState(MapStateRepository.updaters.clearActiveRestaurant()(mapState))}
                    size="sm"
                    variant="outlined"
                    color="neutral"
                >Sluiten</Button>
            </div>
        </div>
    );
};

export default RestaurantInfoPanel;
