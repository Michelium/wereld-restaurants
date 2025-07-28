import React, {useContext, useState} from 'react';
import {Button, Divider, IconButton, Stack, Typography} from '@mui/joy';
import CloseRoundedIcon from '@mui/icons-material/CloseRounded';
import LocationOnRoundedIcon from '@mui/icons-material/LocationOnRounded';
import {MapContext, MapStateRepository} from "../providers/MapContextProvider";
import {getCountryIconUrl} from "../utils/getCountryIcon";
import RestaurantSuggestionModal from "./RestaurantSuggestionModal";

const RestaurantInfoPanel = () => {
    const {mapState, setMapState} = useContext(MapContext);
    const [showSuggestionModal, setShowSuggestionModal] = useState(false);
    const restaurant = mapState.activeRestaurant;

    if (!restaurant) return null;

    const closePanel = () => {
        setMapState(MapStateRepository.updaters.clearActiveRestaurant()(mapState));
    };

    const googleMapsUrl = restaurant.street && restaurant.houseNumber && restaurant.city
        ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
            `${restaurant.name} ${restaurant.street} ${restaurant.houseNumber}, ${restaurant.postalCode ?? ''} ${restaurant.city}`
        )}`
        : null;

    return (
        <>
            <Stack spacing={0.5}>
                <Stack direction="row" justifyContent="space-between" alignItems="center">
                    <Typography level="title-md">{restaurant.name}</Typography>
                    <IconButton onClick={closePanel} variant="plain" size="sm">
                        <CloseRoundedIcon/>
                    </IconButton>
                </Stack>

                {restaurant.country && (
                    <Stack direction="row" alignItems="center" spacing={1}>
                        <img
                            src={getCountryIconUrl(restaurant.country.code)}
                            alt={restaurant.country.name}
                            width={24}
                            height={16}
                            style={{border: '1px solid #ccc'}}
                        />
                        <Typography level="body-sm">{restaurant.country.name}</Typography>
                    </Stack>
                )}
            </Stack>


            <Divider sx={{my: 1}}/>

            <Stack spacing={1}>
                <Typography level="body-sm" textColor="text.secondary">
                    Adres
                </Typography>
                {restaurant.street && restaurant.houseNumber && restaurant.city ? (
                    <Typography>
                        {restaurant.street} {restaurant.houseNumber}<br/>
                        {restaurant.postalCode} {restaurant.city}
                    </Typography>
                ) : (
                    <Typography color="neutral" level="body-sm" sx={{fontStyle: 'italic'}}>
                        Adres onbekend
                    </Typography>
                )}

                {googleMapsUrl && (
                    <Button
                        component="a"
                        href={googleMapsUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        startDecorator={<LocationOnRoundedIcon/>}
                        variant="outlined"
                        size="sm"
                        sx={{mt: 1, alignSelf: 'flex-start'}}
                    >
                        Google Maps
                    </Button>
                )}

                <Divider sx={{my: 2}}/>

                <RestaurantSuggestionModal
                    restaurant={restaurant}
                    open={showSuggestionModal}
                    onClose={() => setShowSuggestionModal(false)}
                />

                <Stack direction="row" spacing={1} mt={3}>
                    <Button
                        size="sm"
                        variant="solid"
                        color="primary"
                        onClick={() => setShowSuggestionModal(true)}
                    >
                        Verbetering voorstellen
                    </Button>
                    <Button
                        size="sm"
                        variant="plain"
                        onClick={closePanel}
                    >
                        Sluiten
                    </Button>
                </Stack>
            </Stack>
        </>
    );
};

export default RestaurantInfoPanel;
