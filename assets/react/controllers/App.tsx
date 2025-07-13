import {useContext, useEffect, useState} from "react";
import {RestaurantType} from "./types/RestaurantType";
import Restaurants from "./pages/Restaurants";
import {MapContext, MapProvider, MapStateRepository} from "./providers/MapContextProvider";

const AppContent = () => {
    const {mapState, setMapState} = useContext(MapContext);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        fetch('/api/restaurants')
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then((data: RestaurantType[]) => {
                setMapState(MapStateRepository.updaters.setRestaurants(data)(mapState));
                setLoading(false);
            })
            .catch((error) => {
                console.error('There was a problem with the fetch operation:', error);
                setLoading(false);
            });
    }, []);

    if (loading) {
        // TODO loading splashscreen
        return <div>Aan het laden...</div>;
    }

    return (
        <Restaurants/>
    );
}

const App = () => {
    return (
        <MapProvider>
            <AppContent/>
        </MapProvider>
    )
};

export default App;
