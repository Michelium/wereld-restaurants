import React from 'react';
import {Typography} from "@mui/joy";
import FilterContent from "./FilterContent";

const DesktopFilterPanel = () => {
    return (
        <>
            <Typography level="body-md" fontWeight="lg" marginBottom={2}>Filter op land</Typography>
            <FilterContent/>
        </>
    );
};

export default DesktopFilterPanel;
