<?php

return [

    // Couvre toutes tes routes d’API + auth + reset
    'paths' => [
        'api/*',
        'auth/*',
        'password/*',
        'embed/*',
    ],

    'allowed_methods' => ['*'],

    // Utilise l’URL front de ton .env (prod) + tu peux ajouter localhost pour le dev si besoin
    'allowed_origins' => [
        env('APP_FRONTEND_URL', 'http://localhost'), // prod: https://migration.ego-network.org
        'http://localhost',
        'http://127.0.0.1',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
    ],

    'allowed_headers' => ['*'],

    // On n’utilise pas de cookies cross-site
    'supports_credentials' => false,

    'allowed_origins_patterns' => [],

    // IMPORTANT pour lire le refresh côté front
    'exposed_headers' => ['X-Refresh-Token'],

    'max_age' => 0,
];
