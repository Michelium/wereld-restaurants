import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import reactPlugin from "@vitejs/plugin-react";
import ViteYaml from "@modyfi/vite-plugin-yaml";

const LAN_IP = "192.168.68.132";

export default defineConfig({
    base: "/build/",
    plugins: [
        ViteYaml(),
        symfonyPlugin({
            input: ['assets/app.js'],
            refresh: true,
            stimulus: true
        }),
        reactPlugin(),
    ],
    server: {
        host: true,                 // bind 0.0.0.0
        port: 5173,
        strictPort: true,
        origin: `http://${LAN_IP}:5173`,
        cors: true,                 // enable CORS
        headers: {                  // MAKE CORS EXPLICIT
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Methods": "GET,OPTIONS",
            "Access-Control-Allow-Headers": "Content-Type, Authorization",
        },
        hmr: {
            host: LAN_IP,
            port: 5173,
            protocol: "ws",
        },
    },
    build: {
        manifest: true,
        rollupOptions: {
            input: { app: "./assets/app.js" },
        },
    },
});
