import React from 'react';
import {MapContainer, Marker, Popup, TileLayer} from 'react-leaflet';
import {RestaurantType} from '../types/RestaurantType';
import 'leaflet/dist/leaflet.css';
import MarkerClusterGroup from "react-leaflet-cluster";
import {getCountryIcon} from "../utils/getCountryIcon";
import RestaurantPopup from "./RestaurantPopup";

interface RestaurantMapProps {
    restaurants: RestaurantType[];
}

const RestaurantMap = ({restaurants}: RestaurantMapProps) => {

    return (
        <MapContainer
            center={[52.1, 5.1]}
            zoom={7}
            style={{height: '100vh', width: '100%'}}
        >
            <TileLayer
                attribution="&copy; OpenStreetMap contributors"
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />

            <MarkerClusterGroup chunkedLoading>
                {restaurants.map((restaurant) => (
                    <Marker
                        key={restaurant.id}
                        position={[restaurant.latitude, restaurant.longitude]}
                        icon={getCountryIcon(restaurant.country?.code ?? 'unknown')}
                    >
                        <Popup>
                            <RestaurantPopup restaurant={restaurant}/>
                        </Popup>
                    </Marker>
                ))}
            </MarkerClusterGroup>
        </MapContainer>
    );
};

export default RestaurantMap;
