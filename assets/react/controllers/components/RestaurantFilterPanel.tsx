import React, {useContext, useState} from 'react';
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
                disabled={mapState.loading}
                value={mapState.filters.countries}
                onChange={(selected) => {
                    setMapState(MapStateRepository.updaters.setFilterCountries(selected as CountryType[]))
                }}
                placeholder="Selecteer landen..."
            />

            {mapState.loading && <div className="filters__loading">Laden...</div>}
        </div>
    );
};

export default RestaurantFilterPanel;
