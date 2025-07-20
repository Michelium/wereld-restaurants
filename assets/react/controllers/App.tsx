import Restaurants from "./pages/Restaurants";
import {MapProvider} from "./providers/MapContextProvider";
import Header from "./components/Header";
import {useEffect, useState} from "react";
import {loadManifest} from "./utils/assetsUtils";

const AppContent = () => {
    return (
        <>
            <Header/>
            <Restaurants/>
        </>
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
