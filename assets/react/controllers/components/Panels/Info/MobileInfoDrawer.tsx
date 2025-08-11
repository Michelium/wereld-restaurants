import React from 'react';
import {Drawer, Sheet} from "@mui/joy";
import InfoContent from "./InfoContent";
import Slide from '@mui/material/Slide';

interface MobileInfoDrawerProps {
    open: boolean;
    onClose: () => void;
}

const MobileInfoDrawer = ({open, onClose}: MobileInfoDrawerProps) => {
    return (
        <Drawer
            anchor="bottom"
            open={open}
            onClose={onClose}
            slotProps={{
                content: {
                    sx: {
                        height: 'auto',
                        maxHeight: '70dvh',
                        overflow: 'auto',
                        borderTopLeftRadius: 16,
                        borderTopRightRadius: 16,
                        width: 'min(480px, 100%)',
                        mx: 'auto',
                    }
                }
            }}
        >
            <Sheet sx={{p: 2}}>
                <InfoContent onClose={onClose} />
            </Sheet>
        </Drawer>
    );
};

export default MobileInfoDrawer;
