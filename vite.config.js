import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import reactPlugin from "@vitejs/plugin-react";
import { viteStaticCopy } from "vite-plugin-static-copy";
import ViteYaml from "@modyfi/vite-plugin-yaml";

export default defineConfig({
    plugins: [
        ViteYaml(),
        symfonyPlugin({
            input: ['assets/app.js'],
            refresh: true,
            stimulus: true
        }),
        reactPlugin(),
    ],
    build: {
        manifest: true,
        rollupOptions: {
            input: {
                app: "./assets/app.js",
            }
        }
    }
});
