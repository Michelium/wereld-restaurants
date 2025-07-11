import React from 'react';
import Select from 'react-select';
import { CountryType } from '../types/CountryType';
import { getCountryIconUrl } from '../utils/getCountryIcon';
import '../../../styles/components/RestaurantFilters.scss';

interface RestaurantFiltersProps {
    selected: string[];
    onChange: (selected: string[]) => void;
    countries: CountryType[];
}

const RestaurantFilters = ({ selected, onChange, countries }: RestaurantFiltersProps) => {
    const options = countries.map((country) => ({
        value: country.code,
        label: country.name,
        icon: getCountryIconUrl(country.code),
    }));

    const selectedOptions = options.filter((opt) => selected.includes(opt.value));

    return (
        <div className="filters">
            <p className="filters__label">Filter op land</p>
            <Select
                isMulti
                options={options}
                value={selectedOptions}
                onChange={(selected) => onChange(selected.map((opt) => opt.value))}
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

export default RestaurantFilters;
