import React, { useEffect, useState } from 'react';
import { RestaurantType } from "../types/RestaurantType";
import { CountryType } from "../types/CountryType";
import RestaurantMap from "../components/RestaurantMap";
import RestaurantFilters from "../components/RestaurantFilters";
import '../../../styles/pages/Restaurants.scss';

interface RestaurantsProps {
    allRestaurants: RestaurantType[];
}

const Restaurants = ({ allRestaurants }: RestaurantsProps) => {
    const [restaurants, setRestaurants] = useState<RestaurantType[]>(allRestaurants);
    const [selectedCountries, setSelectedCountries] = useState<string[]>([]);
    const [countries, setCountries] = useState<CountryType[]>([]);
    const [restaurantInfoOpen, setRestaurantInfoOpen] = useState<boolean>(true);

    useEffect(() => {
        fetch('/api/countries')
            .then((res) => res.json())
            .then(setCountries)
            .catch((err) => console.error('Failed to load countries', err));
    }, []);

    useEffect(() => {
        console.log('Selected countries:', selectedCountries);

        const params = new URLSearchParams();
        selectedCountries.forEach(code => params.append('countries[]', code));

        fetch(`/api/restaurants?${params.toString()}`)
            .then(res => res.json())
            .then(setRestaurants)
            .catch(console.error);
    }, [selectedCountries]);

    return (
        <div className="restaurants-map-wrapper">
            <div className="restaurants-map-wrapper__overlay restaurants-map-wrapper__overlay--filters">
                <RestaurantFilters
                    selected={selectedCountries}
                    onChange={setSelectedCountries}
                    countries={countries}
                />
            </div>
            {restaurantInfoOpen && (
                <div className="restaurants-map-wrapper__overlay restaurants-map-wrapper__overlay--info">
                    <div className="restaurants-map-wrapper__overlay-content">
                        <h2>Restaurant Info</h2>
                        <p>Hier komt de informatie over het restaurant.</p>
                        <button onClick={() => setRestaurantInfoOpen(false)}>Sluiten</button>
                    </div>
                </div>
            )}
            <div className="restaurants-map">
                <RestaurantMap restaurants={restaurants} />
            </div>
        </div>
    );
};

export default Restaurants;
