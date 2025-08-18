import React, {useContext, useState} from 'react';
import {Badge, Button, Drawer, IconButton, Sheet, Stack, Typography} from '@mui/joy';
import FilterListIcon from '@mui/icons-material/FilterList';
import CloseIcon from '@mui/icons-material/Close';
import FilterContent from "./FilterContent";
import {MapContext} from "../../../providers/MapContextProvider";

const MobileFilterButton = () => {
    const {mapState,} = useContext(MapContext);
    const activeCount = mapState.filters.countries?.length ?? 0;

    const [open, setOpen] = useState(false);

    return (
        <>
            <Badge badgeContent={activeCount || null} color="primary" variant="solid">
                <Button size="sm" variant="soft" startDecorator={<FilterListIcon/>} onClick={() => setOpen(true)}>
                    Filters
                </Button>
            </Badge>

            <Drawer
                anchor="bottom"
                open={open}
                onClose={() => setOpen(false)}
                disableEnforceFocus
                slotProps={{
                    content: {
                        sx: {
                            height: 'auto',
                            maxHeight: '60dvh',
                            borderTopLeftRadius: 16,
                            borderTopRightRadius: 16,
                        }
                    }
                }}
            >
                <Sheet sx={{p: 2}}>
                    <Stack direction="row" alignItems="center" justifyContent="space-between" mb={1}>
                        <Typography level="title-md">Filter op land</Typography>
                        <IconButton variant="plain" onClick={() => setOpen(false)}>
                            <CloseIcon/>
                        </IconButton>
                    </Stack>
                    <FilterContent/>
                    <Button sx={{mt: 1}} onClick={() => setOpen(false)} fullWidth>Toepassen</Button>
                </Sheet>
            </Drawer>
        </>
    );
};

export default MobileFilterButton;
