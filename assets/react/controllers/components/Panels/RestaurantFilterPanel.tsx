import React from 'react';
import {CountryType} from '../../types/CountryType';
import {useMediaQuery} from '@mui/material';
import DesktopFilterPanel from "./Filters/DesktopFilterPanel";
import MobileFilterButton from "./Filters/MobileFilterButton";

const RestaurantFilterPanel = () => {
    const isDesktop = useMediaQuery('(min-width: 768px)');

    return isDesktop ? <DesktopFilterPanel/> : <MobileFilterButton/>;
};

export default RestaurantFilterPanel;
