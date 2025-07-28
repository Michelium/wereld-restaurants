import React, { useEffect, useState } from 'react';
import {
    Button, Divider, FormLabel, Input, Stack,
    Textarea, Typography
} from '@mui/joy';
import { RestaurantType } from '../types/RestaurantType';
import CountrySelect from './CountrySelect';
import { CountryType } from '../types/CountryType';
import toast from 'react-hot-toast';

interface RestaurantSuggestionFormProps {
    restaurant: RestaurantType;
    onClose: () => void;
}

const RestaurantSuggestionForm = ({ restaurant, onClose }: RestaurantSuggestionFormProps) => {
    const [submitting, setSubmitting] = useState(false);

    const [name, setName] = useState(restaurant?.name || '');
    const [street, setStreet] = useState(restaurant?.street || '');
    const [houseNumber, setHouseNumber] = useState(restaurant?.houseNumber || '');
    const [postalCode, setPostalCode] = useState(restaurant?.postalCode || '');
    const [city, setCity] = useState(restaurant?.city || '');
    const [countryId, setCountryId] = useState<number | null>(restaurant?.country?.id ?? null);
    const [comment, setComment] = useState('');

    useEffect(() => {
        setName(restaurant?.name || '');
        setStreet(restaurant?.street || '');
        setHouseNumber(restaurant?.houseNumber || '');
        setPostalCode(restaurant?.postalCode || '');
        setCity(restaurant?.city || '');
        setCountryId(restaurant?.country?.id ?? null);
        setComment('');
        setSubmitting(false);
    }, [restaurant]);

    const handleSubmit = async () => {
        if (!name) return;

        setSubmitting(true);

        const fields = {
            name,
            countryId,
            street,
            houseNumber,
            postalCode,
            city
        };

        try {
            const response = await fetch('/api/restaurant-suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    restaurantId: restaurant?.id ?? null,
                    comment,
                    newRestaurant: false,
                    fields
                })
            });

            if (!response.ok) {
                const data = await response.json().catch(() => null);
                const message = data?.message || `Fout bij verzenden (status ${response.status})`;
                toast.error(message);
                return;
            }

            toast.success('Suggestie succesvol verzonden!');
            onClose();

        } catch (err) {
            toast.error('Er is een onverwachte fout opgetreden.');
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <>
            <Typography level="h4">Verbetering voorstellen</Typography>
            <Typography level="body-md" sx={{ mb: 1 }}>
                Vul de onderstaande gegevens in om een verbetering voor te stellen voor dit restaurant.
                Je kunt bestaande gegevens aanpassen of nieuwe gegevens toevoegen.
                Deze suggestie wordt beoordeeld door onze beheerders.
            </Typography>

            <Divider />

            <Stack spacing={1.5} mt={2}>
                <Typography level="body-sm">Algemene gegevens</Typography>

                <FormLabel htmlFor="restaurant-name">Naam:</FormLabel>
                <Input
                    id="restaurant-name"
                    placeholder="Naam van het restaurant"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                />

                <FormLabel htmlFor="restaurant-country">Land van de keuken:</FormLabel>
                <CountrySelect
                    value={restaurant.country || null}
                    onChange={(selected) => {
                        const selectedCountry = selected as CountryType;
                        setCountryId(selectedCountry?.id ?? null);
                    }}
                    id="restaurant-country"
                    placeholder="Land"
                />
            </Stack>

            <Stack spacing={1.5} mt={2}>
                <Typography level="body-sm">Adresgegevens</Typography>

                <FormLabel htmlFor="restaurant-street">Straat:</FormLabel>
                <Input
                    id="restaurant-street"
                    placeholder="Straat"
                    value={street}
                    onChange={(e) => setStreet(e.target.value)}
                />

                <FormLabel htmlFor="restaurant-housenumber">Huisnummer:</FormLabel>
                <Input
                    id="restaurant-housenumber"
                    placeholder="Huisnummer"
                    value={houseNumber}
                    onChange={(e) => setHouseNumber(e.target.value)}
                />

                <FormLabel htmlFor="restaurant-postalcode">Postcode:</FormLabel>
                <Input
                    id="restaurant-postalcode"
                    placeholder="Postcode (bijv. 1234 AB)"
                    value={postalCode}
                    onChange={(e) => setPostalCode(e.target.value)}
                />

                <FormLabel htmlFor="restaurant-city">Plaats:</FormLabel>
                <Input
                    id="restaurant-city"
                    placeholder="Plaats"
                    value={city}
                    onChange={(e) => setCity(e.target.value)}
                />
            </Stack>

            <Stack spacing={1.5} mt={2}>
                <Typography level="body-sm">Heb je nog een opmerking of extra informatie?</Typography>
                <FormLabel htmlFor="restaurant-comment">Opmerking:</FormLabel>
                <Textarea
                    id="restaurant-comment"
                    placeholder="Opmerking"
                    minRows={4}
                    value={comment}
                    onChange={(e) => setComment(e.target.value)}
                />
            </Stack>

            <Stack direction="row" spacing={1} justifyContent="flex-end" mt={3}>
                <Button variant="plain" onClick={onClose}>
                    Annuleer
                </Button>
                <Button
                    variant="solid"
                    onClick={handleSubmit}
                    loading={submitting}
                    disabled={!name || submitting}
                >
                    Versturen
                </Button>
            </Stack>
        </>
    );
};

export default RestaurantSuggestionForm;
