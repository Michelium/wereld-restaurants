import {CountryType} from "./CountryType";

export type RestaurantType = {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    country: CountryType | null;
    countryName: string | null;
    street: string | null;
    houseNumber: string | null;
    postalCode: string | null;
    city: string | null;
    website: string | null;
}
