import React, {useContext} from 'react';
import {CountryType} from '../types/CountryType';
import '../../../styles/components/RestaurantFilterPanel.scss';
import {MapContext, MapStateRepository} from '../providers/MapContextProvider';
import CountrySelect from "./CountrySelect";

const RestaurantFilterPanel = () => {
    const {mapState, setMapState} = useContext(MapContext);

    return (
        <div className="filters">
            <p className="filters__label">Filter op land</p>
            <CountrySelect
                isMulti
                value={mapState.filters.countries}
                onChange={(selected) =>
                    setMapState(
                        MapStateRepository.updaters.setFilterCountries(selected as CountryType[])(mapState)
                    )
                }
                placeholder="Selecteer landen..."
            />
        </div>
    );
};

export default RestaurantFilterPanel;
