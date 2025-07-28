import React, {useContext} from 'react';
import {CountryType} from '../types/CountryType';
import {MapContext, MapStateRepository} from '../providers/MapContextProvider';
import CountrySelect from './CountrySelect';
import {Stack, Typography} from '@mui/joy';

const RestaurantFilterPanel = () => {
    const {mapState, setMapState} = useContext(MapContext);

    return (
        <Stack spacing={1}>
            <Typography level="body-md" fontWeight="lg">
                Filter op land
            </Typography>

            <CountrySelect
                isMulti
                disabled={mapState.loading}
                value={mapState.filters.countries}
                onChange={(selected) => {
                    setMapState(
                        MapStateRepository.updaters.setFilterCountries(selected as CountryType[])
                    );
                }}
                placeholder="Selecteer landen..."
                showRestaurantCount
            />
        </Stack>
    );
};

export default RestaurantFilterPanel;
