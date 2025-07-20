import React, {useContext, useEffect, useState} from 'react';
import {MultiValue} from 'react-select';
import {CountryType} from '../types/CountryType';
import '../../../styles/components/RestaurantFilterPanel.scss';
import {MapContext, MapStateRepository} from '../providers/MapContextProvider';
import CountrySelect from "./CountrySelect";

const RestaurantFilterPanel = () => {
    const [countries, setCountries] = useState<CountryType[]>([]);
    const {mapState, setMapState} = useContext(MapContext);

    useEffect(() => {
        fetch('/api/countries')
            .then(res => res.json())
            .then(setCountries)
            .catch(err => console.error('Failed to load countries', err));
    }, []);

    const handleSelectChange = (selected: MultiValue<CountryType> | null) => {
        const mutableSelected = selected ? [...selected] : [];

        setMapState(MapStateRepository.updaters.setFilterCountries(mutableSelected)(mapState));
    };

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
