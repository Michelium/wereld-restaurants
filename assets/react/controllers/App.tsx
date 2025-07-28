import Restaurants from "./pages/Restaurants";
import {MapProvider} from "./providers/MapContextProvider";
import {useEffect, useState} from "react";
import {loadManifest} from "./utils/assetsUtils";
import {Toaster} from "react-hot-toast";

const AppContent = () => {
    return (
        <Restaurants/>
    );
}

const App = () => {
    const [manifestLoaded, setManifestLoaded] = useState(false);

    useEffect(() => {
        loadManifest().then(() => setManifestLoaded(true));
    }, []);

    if (!manifestLoaded) return;

    return (
        <MapProvider>
            <Toaster/>
            <AppContent/>
        </MapProvider>
    )
};

export default App;
