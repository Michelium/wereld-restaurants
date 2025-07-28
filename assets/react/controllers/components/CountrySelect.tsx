import React, {useEffect, useState} from 'react';
import Select from 'react-select';
import {CountryType} from '../types/CountryType';
import {getCountryIconUrl} from '../utils/getCountryIcon';

interface CountrySelectProps {
    isMulti?: boolean;
    value: CountryType | CountryType[] | null;
    onChange: (value: CountryType | CountryType[] | null) => void;
    placeholder?: string;
    id?: string;
    disabled?: boolean;
}

const CountrySelect = ({isMulti = false, value, onChange, placeholder = 'Select country...', id, disabled}: CountrySelectProps) => {
    const [countries, setCountries] = useState<CountryType[]>([]);

    useEffect(() => {
        fetch('/api/countries')
            .then(res => res.json())
            .then(setCountries)
            .catch(err => console.error('Failed to load countries', err));
    }, []);

    const handleChange = (selected: any) => {
        onChange(selected);
    };

    return (
        <Select<CountryType, boolean>
            isMulti={isMulti}
            options={countries}
            getOptionLabel={(country) => country.name}
            getOptionValue={(country) => country.code}
            value={value}
            onChange={handleChange}
            classNamePrefix="country-select"
            placeholder={placeholder}
            menuPortalTarget={document.body}
            isDisabled={disabled}
            id={id}
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
    );
};

export default CountrySelect;
