import React from 'react';
import { Sheet, Typography } from '@mui/joy';

const ZoomPrompt = () => {
    return (
        <Sheet
            variant="soft"
            color="neutral"
            sx={{
                position: 'absolute',
                top: '40px',
                left: '50%',
                transform: 'translateX(-50%)',
                zIndex: 1000,
                padding: '0.75rem 1.5rem',
                borderRadius: 'md',
                boxShadow: 'lg',
                maxWidth: '90%',
                textAlign: 'center',
            }}
        >
            <Typography level="body-md" fontWeight="lg">
                Zoom in of filter op land om restaurants te zien.
            </Typography>
        </Sheet>
    );
};

export default ZoomPrompt;
