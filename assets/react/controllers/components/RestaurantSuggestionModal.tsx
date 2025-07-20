import React, {useEffect, useState} from 'react';
import {RestaurantType} from "../types/RestaurantType";
import {Button, Divider, FormLabel, Input, Modal, ModalClose, ModalDialog, Stack, Textarea, Typography} from "@mui/joy";
import CountrySelect from "./CountrySelect";
import {CountryType} from "../types/CountryType";
import toast from 'react-hot-toast';

interface RestaurantSuggestionModalProps {
    restaurant: RestaurantType | null;
    onClose: () => void;
    open: boolean;
}

const RestaurantSuggestionModal = ({restaurant, onClose, open}: RestaurantSuggestionModalProps) => {
    const [submitting, setSubmitting] = useState(false);

    const [name, setName] = useState(restaurant?.name || '');
    const [street, setStreet] = useState(restaurant?.street || '');
    const [houseNumber, setHouseNumber] = useState(restaurant?.houseNumber || '');
    const [postalCode, setPostalCode] = useState(restaurant?.postalCode || '');
    const [city, setCity] = useState(restaurant?.city || '');
    const [countryId, setCountryId] = useState<number | null>(restaurant?.country?.id ?? null);

    const [comment, setComment] = useState('');

    // Reset fields when modal opens
    useEffect(() => {
        if (!open) return;

        setName(restaurant?.name || '');
        setStreet(restaurant?.street || '');
        setHouseNumber(restaurant?.houseNumber || '');
        setPostalCode(restaurant?.postalCode || '');
        setCity(restaurant?.city || '');
        setCountryId(restaurant?.country?.id ?? null);
        setSubmitting(false);
        setComment('');
    }, [restaurant, open]);

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
        <Modal open={open} onClose={onClose}>
            <ModalDialog
                sx={{
                    maxHeight: '90vh',
                    overflowY: 'auto',
                    overflowX: 'hidden',
                    paddingRight: '8px',
                }}
            >
                <ModalClose/>
                <Typography level="h4">Verbetering voorstellen</Typography>
                <Typography level="body-md">
                    Vul de onderstaande gegevens in om een verbetering voor te stellen voor dit restaurant.
                    <br/>
                    Je kunt bestaande gegevens aanpassen of nieuwe gegevens toevoegen.
                </Typography>
                <Typography level="body-md">
                    Deze suggestie wordt beoordeeld door onze beheerders en kan worden toegevoegd aan de database.
                </Typography>

                <Divider/>

                <Stack spacing={1.5} mt={1}>
                    <Typography level="body-sm">Algemene gegevens</Typography>

                    <FormLabel htmlFor="restaurant-name">Naam:</FormLabel>
                    <Input
                        placeholder="Naam van het restaurant"
                        id="restaurant-name"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                    />

                    <FormLabel htmlFor="restaurant-country">Land van de keuken:</FormLabel>
                    <CountrySelect
                        value={restaurant?.country || null}
                        onChange={(selected) => {
                            const selectedCountry = selected as CountryType;
                            setCountryId(selectedCountry?.id ?? null);
                        }}
                        id="restaurant-country"
                        placeholder="Land"
                    />
                </Stack>

                <Stack spacing={1.5} mt={1}>
                    <Typography level="body-sm">Adresgegevens</Typography>
                    <FormLabel htmlFor="restaurant-street">Straat:</FormLabel>
                    <Input
                        placeholder="Straat"
                        id="restaurant-street"
                        value={street}
                        onChange={(e) => setStreet(e.target.value)}
                    />

                    <FormLabel htmlFor="restaurant-housenumber">Huisnummer:</FormLabel>
                    <Input
                        placeholder="Huisnummer"
                        id="restaurant-housenumber"
                        value={houseNumber}
                        onChange={(e) => setHouseNumber(e.target.value)}
                    />

                    <FormLabel htmlFor="restaurant-postalcode">Postcode:</FormLabel>
                    <Input
                        placeholder="Postcode (bijv. 1234 AB)"
                        id="restaurant-postalcode"
                        value={postalCode}
                        onChange={(e) => setPostalCode(e.target.value)}
                    />

                    <FormLabel htmlFor="restaurant-city">Plaats:</FormLabel>
                    <Input
                        placeholder="Plaats"
                        id="restaurant-city"
                        value={city}
                        onChange={(e) => setCity(e.target.value)}
                    />
                </Stack>

                <Stack spacing={1.5} mt={1}>
                    <Typography level="body-sm">Heb je nog een opmerking of extra informatie over dit restaurant?</Typography>
                    <FormLabel htmlFor="restaurant-street">Opmerking:</FormLabel>
                    <Textarea
                        placeholder="Opmerking"
                        id="restaurant-comment"
                        value={comment}
                        minRows={4}
                        onChange={(e) => setComment(e.target.value)}
                    />
                </Stack>

                <Stack direction="row" spacing={1} justifyContent="flex-end" mt={2}>
                    <Button variant="plain" onClick={onClose}>Annuleer</Button>
                    <Button
                        variant="solid"
                        onClick={handleSubmit}
                        loading={submitting}
                        disabled={!name || submitting}
                    >
                        Versturen
                    </Button>
                </Stack>
            </ModalDialog>
        </Modal>
    );
};

export default RestaurantSuggestionModal;
