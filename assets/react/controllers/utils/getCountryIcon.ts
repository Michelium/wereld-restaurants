import L from "leaflet";

const flags = import.meta.glob('/assets/images/flags/*.png', {
    eager: true,
    query: '?url',
    import: 'default', // get the string URL, not a module
}) as Record<string, string>;

const norm = (code: string) => code.toLowerCase().split(/[-_]/)[0];

const flagUrl = (code: string, fallback = 'xx'): string => {
    const k1 = `/assets/images/flags/${norm(code)}.png`;
    const k2 = `/assets/images/flags/${fallback}.png`;
    return flags[k1] ?? flags[k2]!;
};

export const getCountryIconUrl = (code: string) => flagUrl(code);

export const getCountryIcon = (code: string): L.Icon =>
    new L.Icon({
        iconUrl: flagUrl(code),
        iconSize: [24, 18],
        iconAnchor: [9, 18],
        popupAnchor: [0, -18],
        className: 'leaflet-flag-icon',
    });
