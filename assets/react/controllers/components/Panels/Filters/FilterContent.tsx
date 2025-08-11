import {Button, Stack} from '@mui/joy';
import React, {useContext} from 'react';
import {MapContext, MapStateRepository} from "../../../providers/MapContextProvider";
import CountrySelect from "../../Controls/CountrySelect";

const FilterContent = () => {
    const {mapState, setMapState} = useContext(MapContext);
    const activeCount = mapState.filters.countries?.length ?? 0;

    return (
        <Stack spacing={1}>
            <CountrySelect
                isMulti
                disabled={mapState.loading}
                value={mapState.filters.countries}
                onChange={(selected) =>
                    setMapState(MapStateRepository.updaters.setFilterCountries(selected as any))
                }
                placeholder="Selecteer landen..."
                showRestaurantCount
            />
            {activeCount > 0 && (
                <Button
                    size="sm"
                    variant="plain"
                    onClick={() => setMapState(MapStateRepository.updaters.setFilterCountries([]))}
                    sx={{alignSelf: 'flex-start'}}
                >
                    Filters wissen
                </Button>
            )}
        </Stack>
    );
};

export default FilterContent;
