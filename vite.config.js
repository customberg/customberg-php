import { resolve } from 'path'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import react from '@vitejs/plugin-react';
import createExternal from 'vite-plugin-external';

export default defineConfig({
    plugins: [
        createExternal({
            externals: {
                react: 'React',
                'react-dom': 'ReactDOM',
                'laraberg': 'Laraberg',
            }
        }),
        // react(),
        laravel({
            input: [
                'resources/js/customberg.jsx',
            ],
            publicDirectory: 'resources',
            buildDirectory: 'dist',
        }),
    ],
    build: {
        lib: {
            entry: resolve(__dirname, 'resources/js/customberg.jsx'),
            name: 'CustombergEditFields',
            fileName: 'customberg',
        },
    },

    // resolve: {
    //     alias: {
    //         'react': 'https://unpkg.com/react@17.0.2/umd/react.production.min.js',
    //         'react-dom': 'https://unpkg.com/react-dom@17.0.2/umd/react-dom.production.min.js'
    //     },
    // },
    // build: {
    //     rollupOptions: {
    //         external: ["react", "react-dom"],
    //         output: {
    //             globals: {
    //                 react: "React",
    //                 "react-dom": "ReactDOM",
    //             },
    //         },
    //     },
    // },
});
