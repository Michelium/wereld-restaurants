import React from 'react';
import {CountryType} from '../../../types/CountryType';
import {useMediaQuery} from '@mui/material';
import DesktopFilterPanel from "./DesktopFilterPanel";
import MobileFilterButton from "./MobileFilterButton";

const RestaurantFilterPanel = () => {
    const isDesktop = useMediaQuery('(min-width: 768px)');

    return isDesktop ? <DesktopFilterPanel/> : <MobileFilterButton/>;
};

export default RestaurantFilterPanel;
