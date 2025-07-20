import Restaurants from "./pages/Restaurants";
import {MapProvider} from "./providers/MapContextProvider";
import {useEffect, useState} from "react";
import {loadManifest} from "./utils/assetsUtils";

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
            <AppContent/>
        </MapProvider>
    )
};

export default App;
