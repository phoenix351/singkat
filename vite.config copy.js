import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.jsx",
            refresh: true,
        }),
        react(),
    ],
    server: {
        https: true, // Ensure the Vite dev server uses HTTPS
        host: "0.0.0.0", // Optional: Adjust host settings for your environment
    },
    base: "/sdm/build/",
    // base: "/",
});
