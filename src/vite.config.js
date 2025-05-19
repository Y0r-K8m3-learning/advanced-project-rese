import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        host: "0.0.0.0", // Dockerなどで外部からアクセスするために必要
        port: 5173,
        strictPort: true,
        origin: "http://localhost:5173", // 👈 明示的にoriginを指定
        cors: {
            origin: ["http://localhost"], // 👈 Laravelが動いてるポートを許可
        },
        watch: {
            usePolling: true,
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
