import React, {useContext, useEffect, useState} from 'react';
import Select, {MultiValue} from 'react-select';
import {CountryType} from '../types/CountryType';
import {getCountryIconUrl} from '../utils/getCountryIcon';
import '../../../styles/components/RestaurantFilterPanel.scss';
import {MapContext, MapStateRepository} from '../providers/MapContextProvider';

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
            <Select<CountryType, true>
                isMulti
                options={countries}
                getOptionLabel={(country) => country.name}
                getOptionValue={(country) => country.code}
                value={mapState.filters.countries}
                onChange={handleSelectChange}
                classNamePrefix="country-select"
                placeholder="Selecteer landen..."
                menuPortalTarget={document.body}
                styles={{
                    menuPortal: (base) => ({
                        ...base,
                        zIndex: 2000,
                    }),
                    option: (base) => ({
                        ...base,
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px',
                    }),
                    multiValueLabel: (base) => ({
                        ...base,
                        display: 'flex',
                        alignItems: 'center',
                        gap: '6px',
                    }),
                }}
                formatOptionLabel={(country) => (
                    <div style={{display: 'flex', alignItems: 'center', gap: '8px'}}>
                        <img
                            src={getCountryIconUrl(country.code)}
                            alt={country.name}
                            style={{width: 20, height: 14, border: '1px solid #ccc'}}
                        />
                        {country.name}
                    </div>
                )}
            />
        </div>
    );
};

export default RestaurantFilterPanel;
