import Restaurants from "./pages/Restaurants";
import {MapProvider} from "./providers/MapContextProvider";
import {Toaster} from "react-hot-toast";

const AppContent = () => {
    return (
        <Restaurants/>
    );
}

const App = () => {
    return (
        <MapProvider>
            <Toaster/>
            <AppContent/>
        </MapProvider>
    )
};

export default App;
