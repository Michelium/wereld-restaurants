import {useEffect, useState} from "react";
import {RestaurantType} from "./types/RestaurantType";
import RestaurantMap from "./components/RestaurantMap";
import Restaurants from "./pages/Restaurants";

const App = () => {

    const [restaurants, setRestaurants] = useState<RestaurantType[]>([]);
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
                setRestaurants(data);
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

    return <Restaurants allRestaurants={restaurants}/>
};

export default App;
