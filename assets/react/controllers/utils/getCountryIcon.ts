import L from 'leaflet';
import {getAssetPath} from "./assetsUtils";

export const getCountryIconUrl = (code: string): string => {
    const filename = `images/flags/${code.toLowerCase()}.png`;
    return getAssetPath(filename);
}

export const getCountryIcon = (code: string): L.Icon => {
    return new L.Icon({
        iconUrl: getCountryIconUrl(code),
        iconSize: [24, 18],
        iconAnchor: [9, 18],
        popupAnchor: [0, -18],
        className: 'leaflet-flag-icon'
    });
};
