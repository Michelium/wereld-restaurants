import React from 'react';
import {Divider, Modal, ModalClose, ModalDialog, Typography} from '@mui/joy';
import RestaurantSuggestionForm from "./RestaurantSuggestionForm";

interface RestaurantSuggestionModalProps {
    onClose: () => void;
    open: boolean;
}

const NewRestaurantModal = ({onClose, open}: RestaurantSuggestionModalProps) => {
    return (
        <Modal open={open} onClose={onClose}>
            <ModalDialog
                sx={{
                    maxHeight: '90vh',
                    overflowY: 'auto',
                    overflowX: 'hidden',
                }}
            >
                <ModalClose/>

                <Typography level="h4">Nieuw restaurant toevoegen</Typography>
                <Typography level="body-md" sx={{mb: 1}}>
                    Vul de onderstaande gegevens in om een nieuw restaurant toe te voegen.
                    <br/>
                    Hoe meer informatie je geeft hoe beter we het restaurant kunnen toevoegen.
                    <br/><br/>
                    We willen je alvast bedanken voor je bijdrage aan deze website!
                </Typography>

                <Divider/>

                <RestaurantSuggestionForm restaurant={null} onClose={onClose}/>
            </ModalDialog>
        </Modal>
    );
};

export default NewRestaurantModal;
