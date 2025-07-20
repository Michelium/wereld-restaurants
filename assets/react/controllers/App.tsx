import Restaurants from "./pages/Restaurants";
import {MapProvider} from "./providers/MapContextProvider";
import Header from "./components/Header";

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
