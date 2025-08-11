import React from 'react';
import {Divider, Modal, ModalClose, ModalDialog, Typography} from '@mui/joy';
import RestaurantSuggestionForm from "../Forms/RestaurantSuggestionForm";

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
                    width: {xs: '95vw', sm: 'auto'},
                }}
            >
                <ModalClose/>

                <Typography level="h4">Nieuw restaurant toevoegen</Typography>
                <Typography level="body-md" sx={{mb: 1}}>
                    Vul de onderstaande gegevens in om een nieuw restaurant toe te voegen.
                    <br/><br/>
                    Des te meer informatie je toevoegt, des te beter kunnen we het restaurant beoordelen en toevoegen.
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
