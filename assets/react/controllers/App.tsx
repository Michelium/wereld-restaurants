import Restaurants from "./pages/Restaurants";
import {MapProvider} from "./providers/MapContextProvider";

const AppContent = () => {
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
