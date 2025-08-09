import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import reactPlugin from "@vitejs/plugin-react";
import { viteStaticCopy } from "vite-plugin-static-copy";
import ViteYaml from "@modyfi/vite-plugin-yaml";

export default defineConfig({
    // server: {
    //     origin: 'http://localhost:5174',
    //     cors: true,
    //     host: 'localhost', // or '127.0.0.1' to avoid [::1] issues
    //     port: 5174
    // },
    plugins: [
        ViteYaml(),
        symfonyPlugin({
            input: ['assets/app.js'],
            refresh: true,
            stimulus: true
        }),
        reactPlugin(),
        viteStaticCopy({
            targets: [
                {
                    src: "assets/images/**/*",
                    dest: "images"
                }
            ]
        })
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
