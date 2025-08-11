import React from 'react';
import { Sheet, Typography } from '@mui/joy';

const ZoomPrompt = () => {
    return (
        <Sheet
            variant="soft"
            color="neutral"
            sx={{
                position: 'absolute',
                zIndex: 1000,
                boxShadow: 'lg',
                borderRadius: 'md',
                p: '0.75rem 1rem',
                textAlign: 'center',

                top: { xs: '5rem', md: '1rem' },
                left: { xs: 8, md: '50%' },
                right: { xs: 8, md: 'auto' },
                transform: { xs: 'none', md: 'translateX(-50%)' },
                maxWidth: { md: '90%' },
            }}
        >
            <Typography level="body-md" fontWeight="lg">
                Zoom in of filter op land om restaurants te zien.
            </Typography>
        </Sheet>
    );
};

export default ZoomPrompt;
