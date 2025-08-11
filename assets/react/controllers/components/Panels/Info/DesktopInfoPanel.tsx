import React from 'react';
import InfoContent from "./InfoContent";

const DesktopInfoPanel = () => {
    return (
        <div className="restaurants-map-wrapper__overlay restaurants-map-wrapper__overlay--info">
            <InfoContent
                onClose={() => {}} // No-op for desktop, as there's no close action
            />
        </div>
    );
};

export default DesktopInfoPanel;
