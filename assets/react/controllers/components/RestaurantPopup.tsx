import React from 'react';
import {RestaurantType} from '../types/RestaurantType';
import {getCountryIconUrl} from "../utils/getCountryIcon";
import '../../../styles/components/RestaurantPopup.scss';

interface RestaurantPopupProps {
    restaurant: RestaurantType;
}

const RestaurantPopup = ({restaurant}: RestaurantPopupProps) => {
    const {name, street, houseNumber, postalCode, city, country} = restaurant;

    return (
        <div className="popup">
            <div className="popup__header">
                <strong className="popup__title">{name}</strong>
                {country && (
                    <img
                        src={getCountryIconUrl(country.code)}
                        alt={country.code}
                        className="popup__flag"
                    />
                )}
            </div>

            <div className="popup__address">
                {street && houseNumber && postalCode && city ? (
                    <>
                        <p>
                            {street} {houseNumber}<br/>
                            {postalCode} {city}
                        </p>

                        <a
                            href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(name + ' ' + street + ' ' + houseNumber + ', ' + postalCode + ' ' + city)}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="popup__address-link"
                        >
                            Google Maps
                        </a>
                    </>
                ) : 'Adres onbekend'}
            </div>

            <div className="popup__country">
                {country?.name ?? 'Onbekend land'}
            </div>
        </div>
    );
};

export default RestaurantPopup;
