import React, {useEffect, useState} from 'react';
import {Button, Divider, Modal, ModalClose, ModalDialog, Stack, Typography} from '@mui/joy';
import {RestaurantType} from "../types/RestaurantType";
import toast from "react-hot-toast";
import RestaurantSuggestionForm from "./RestaurantSuggestionForm";

interface RestaurantSuggestionModalProps {
    restaurant: RestaurantType | null;
    onClose: () => void;
    open: boolean;
}

type SuggestionType = 'form' | 'closed';

const RestaurantSuggestionModal = ({restaurant, onClose, open}: RestaurantSuggestionModalProps) => {
    const [step, setStep] = useState<null | SuggestionType>(null);

    const handleSimpleSubmit = async (type: SuggestionType) => {
        try {
            const response = await fetch('/api/restaurant-suggestions', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    restaurantId: restaurant?.id,
                    newRestaurant: false,
                    type,
                }),
            });

            if (!response.ok) {
                toast.error('Fout bij verzenden.');
                return;
            }

            toast.success('Bedankt voor je melding! We gaan er mee aan de slag.');
            setStep(null);
            onClose();
        } catch (e) {
            toast.error('Er is een fout opgetreden.');
        }
    };

    if (!restaurant) return null;

    useEffect(() => {
        setStep(null);
    }, [open]);

    return (
        <Modal open={open} onClose={onClose}>
            <ModalDialog
                sx={{
                    maxHeight: '90vh',
                    overflowY: 'auto',
                    overflowX: 'hidden',
                    // paddingRight: '8px',
                }}
            >
                <ModalClose/>
                {step === 'form' ? (
                    <RestaurantSuggestionForm restaurant={restaurant} onClose={onClose}/>
                ) : (
                    <>
                        <Typography level="h4">Melding maken</Typography>
                        <Typography level="body-md">
                            Wat wil je melden over <strong>{restaurant.name}</strong>?
                        </Typography>
                        <Divider sx={{my: 1}}/>
                        <Stack spacing={2}>
                            <Button onClick={() => setStep('form')}>
                                Gegevens kloppen niet
                            </Button>
                            <Button variant="soft" onClick={() => handleSimpleSubmit('closed')}>
                                Restaurant bestaat niet (meer)
                            </Button>
                        </Stack>
                    </>
                )}
            </ModalDialog>
        </Modal>
    );
};

export default RestaurantSuggestionModal;
