let manifest: Record<string, string> = {};

export const loadManifest = async () => {
    try {
        const response = await fetch('/build/manifest.json'); // Adjust the path if needed
        manifest = await response.json();
    } catch (error) {
        console.error('Error loading Webpack manifest:', error);
    }
};

/**
 * Resolves an asset path from the Webpack manifest or returns a direct path for user-uploaded files.
 * @param fileName The original filename (e.g., "images/flag.svg" or "uploads/filename.jpg")
 * @param isUserUploaded Optional parameter to specify if the asset is from the user-uploaded folder
 * @return The versioned file path or the direct path for user-uploaded assets.
 */
export const getAssetPath = (fileName: string, isUserUploaded: boolean = false): string => {
    if (isUserUploaded) {
        // If it's a user-uploaded file, we just return the path relative to the 'public' folder
        return `/images/uploads/${fileName}`; // Assuming the uploaded files are in public/images/uploads
    }

    // If not user-uploaded, try fetching it from the Webpack manifest
    fileName = 'build/' + fileName;
    const assetPath = manifest[fileName];

    if (!assetPath) {
        console.warn(`Asset not found in manifest: ${fileName}`);
        return `/build/${fileName}`;
    }

    return assetPath;
};
