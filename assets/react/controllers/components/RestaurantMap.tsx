import React, {useContext} from 'react';
import {MapContainer, Marker, TileLayer} from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import MarkerClusterGroup from "react-leaflet-cluster";
import {getCountryIcon} from "../utils/getCountryIcon";
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";

const RestaurantMap = () => {
    const {mapState, setMapState} = useContext(MapContext);

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
                {mapState.restaurants.map((restaurant) => (
                    <Marker
                        eventHandlers={{
                            click: () => {
                                setMapState(MapStateRepository.updaters.setActiveRestaurant(restaurant)(mapState));
                            },
                        }}
                        key={restaurant.id}
                        position={[restaurant.latitude, restaurant.longitude]}
                        icon={getCountryIcon(restaurant.country?.code ?? 'unknown')}
                    />
                ))}
            </MarkerClusterGroup>
        </MapContainer>
    );
};

export default RestaurantMap;
