import React, {useContext} from 'react';
import {MapContext, MapStateRepository} from "../../../providers/MapContextProvider";
import {useMediaQuery} from "@mui/material";
import DesktopInfoPanel from "./DesktopInfoPanel";
import MobileInfoDrawer from "./MobileInfoDrawer";

const RestaurantInfoPanel = () => {
    const isDesktop = useMediaQuery('(min-width: 768px)');
    const {mapState, setMapState} = useContext(MapContext);
    const open = Boolean(mapState.activeRestaurant);

    const close = () => {
        setMapState(MapStateRepository.updaters.clearActiveRestaurant());
    };

    if (isDesktop) return open ? <DesktopInfoPanel /> : null;

    return <MobileInfoDrawer open={open} onClose={close}/>;
};

export default RestaurantInfoPanel;
