import React, {useContext, useEffect, useState} from 'react';
import Select from 'react-select';
import { CountryType } from '../types/CountryType';
import { getCountryIconUrl } from '../utils/getCountryIcon';
import '../../../styles/components/RestaurantFilterPanel.scss';
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";

const RestaurantFilterPanel = () => {
    const [countries, setCountries] = useState<CountryType[]>([]);

    useEffect(() => {
        fetch('/api/countries')
            .then((res) => res.json())
            .then(setCountries)
            .catch((err) => console.error('Failed to load countries', err));
    }, []);

    const {mapState, setMapState} = useContext(MapContext);

    const options = countries.map((country) => ({
        value: country.code,
        label: country.name,
        icon: getCountryIconUrl(country.code),
    }));

    const selectedOptions = options.filter((opt) =>
        mapState.filters.countries.some((c) => c.code === opt.value)
    );

    const handleSelectChange = (selected: any) => {
        const selectedCountries: CountryType[] = selected ? selected.map((opt: any) => opt.country) : [];

        setMapState(
            MapStateRepository.updaters.setFilters({
                ...mapState.filters,
                countries: selectedCountries,
            })
        );
    };

    return (
        <div className="filters">
            <p className="filters__label">Filter op land</p>
            <Select
                isMulti
                options={options}
                value={selectedOptions}
                onChange={handleSelectChange}
                classNamePrefix="country-select"
                placeholder="Selecteer landen..."
                styles={{
                    option: (base, state) => ({
                        ...base,
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px',
                    }),
                    singleValue: (base) => ({
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
                formatOptionLabel={({ label, icon }) => (
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <img
                            src={icon}
                            alt={label}
                            style={{ width: 20, height: 14, border: '1px solid #ccc' }}
                        />
                        {label}
                    </div>
                )}
            />
        </div>
    );
};

export default RestaurantFilterPanel;
