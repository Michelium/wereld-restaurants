import React, {useContext} from 'react';
import {MapContainer, Marker, TileLayer} from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import MarkerClusterGroup from "react-leaflet-cluster";
import {getCountryIcon} from "../utils/getCountryIcon";
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";
import MapEffect from "./MapEffect";

const RestaurantMap = () => {
    const {mapState, setMapState} = useContext(MapContext);

    return (
        <MapContainer
            center={[52.1, 5.1]}
            zoom={8}
            minZoom={7}
            maxZoom={16}
            style={{height: '100vh', width: '100%'}}
        >
            <TileLayer
                attribution="&copy; OpenStreetMap contributors"
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />

            <MapEffect
                selectedCountries={mapState.filters.countries}
                onRestaurantsUpdate={(data) =>
                    setMapState(MapStateRepository.updaters.setRestaurants(data)(mapState))
                }
            />

            <MarkerClusterGroup chunkedLoading>
                {mapState.restaurants.map((restaurant) => (
                    <Marker
                        key={restaurant.id}
                        position={[restaurant.latitude, restaurant.longitude]}
                        icon={getCountryIcon(restaurant.country?.code ?? 'unknown')}
                        eventHandlers={{
                            click: () =>
                                setMapState(
                                    MapStateRepository.updaters.setActiveRestaurant(restaurant)(mapState)
                                ),
                        }}
                    />
                ))}
            </MarkerClusterGroup>
        </MapContainer>
    );
};

export default RestaurantMap;
