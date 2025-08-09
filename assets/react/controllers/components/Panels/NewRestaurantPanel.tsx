import React, {useState} from 'react';
import {Button, Stack, Typography} from '@mui/joy';
import NewRestaurantModal from "../Modals/NewRestaurantModal";

const RestaurantFilterPanel = () => {
    const [showNewRestaurantModal, setShowNewRestaurantModal] = useState(false);

    return (
        <Stack spacing={2}>
            <NewRestaurantModal
                onClose={() => setShowNewRestaurantModal(false)}
                open={showNewRestaurantModal}
            />

            <Typography level="body-md" fontWeight="lg">
                Mis je een restaurant?
            </Typography>
            <Typography level="body-sm" textColor="text.secondary">
                Deze website draait op de input van gebruikers. Als je een restaurant mist, kun je deze toevoegen via het formulier hieronder.
            </Typography>
            <Typography level="body-sm" textColor="text.tertiary">
                Je kunt ook bestaande restaurants aanpassen door op een restaurant te klikken en vervolgens op "Verbetering voorstellen" te klikken
            </Typography>
            <Button
                variant="solid"
                color="primary"
                onClick={() => setShowNewRestaurantModal(true)}
            >
                Voeg restaurant toe
            </Button>
        </Stack>
    );
};

export default RestaurantFilterPanel;
