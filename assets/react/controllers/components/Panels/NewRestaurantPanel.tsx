import React, {useState} from 'react';
import {Button, IconButton, Stack, Typography} from '@mui/joy';
import Collapse from '@mui/material/Collapse';
import KeyboardArrowDownIcon from '@mui/icons-material/KeyboardArrowDown';
import NewRestaurantModal from '../Modals/NewRestaurantModal';

const RestaurantFilterPanel = () => {
    const [showNewRestaurantModal, setShowNewRestaurantModal] = useState(false);
    const [expanded, setExpanded] = useState(false);

    return (
        <Stack spacing={1}>
            <NewRestaurantModal
                onClose={() => setShowNewRestaurantModal(false)}
                open={showNewRestaurantModal}
            />

            {/* Title row */}
            <Stack
                direction="row"
                alignItems="center"
                justifyContent="space-between"
                sx={{cursor: 'pointer', userSelect: 'none'}}
                onClick={() => setExpanded((v) => !v)}
            >
                <Typography level="body-md" fontWeight="lg">
                    Mis je een restaurant?
                </Typography>

                <IconButton
                    size="sm"
                    variant="plain"
                    onClick={(e) => {
                        e.stopPropagation(); // avoid double toggle
                        setExpanded((v) => !v);
                    }}
                    sx={{
                        transition: 'transform 200ms ease',
                        transform: expanded ? 'rotate(180deg)' : 'rotate(0deg)',
                    }}
                >
                    <KeyboardArrowDownIcon/>
                </IconButton>
            </Stack>

            {/* Animated body */}
            <Collapse in={expanded} timeout={200} unmountOnExit>
                <Stack spacing={1} sx={{mt: 1}}>
                    <Typography level="body-sm" textColor="text.secondary">
                        Deze website draait op de input van gebruikers. Als je een restaurant mist, kun je deze
                        toevoegen via het formulier hieronder.
                    </Typography>
                    <Typography level="body-sm" textColor="text.tertiary">
                        Je kunt ook bestaande restaurants aanpassen door op een restaurant te klikken en
                        vervolgens op "Verbetering voorstellen" te klikken
                    </Typography>
                    <Button variant="solid" color="primary" onClick={() => setShowNewRestaurantModal(true)}>
                        Voeg restaurant toe
                    </Button>
                </Stack>
            </Collapse>
        </Stack>
    );
};

export default RestaurantFilterPanel;
