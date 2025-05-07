<?php


    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

return [

        'paths' => ['api/*', 'sanctum/csrf-cookie'],
    
        // Méthodes que tu acceptes (POST + GET suffisent le plus souvent)
        'allowed_methods' => ['POST', 'GET', 'OPTIONS'],
    
        // 👉  Autorise UNIQUEMENT le front local
        'allowed_origins' => ['http://localhost'],
    
        // Garder vide : on utilise la liste ci‑dessus, pas les patterns
        'allowed_origins_patterns' => [],
    
        // Tous les headers client OK
        'allowed_headers' => ['*'],
    
        // Rien d’exposé en plus
        'exposed_headers' => [],
    
        'max_age' => 0,
    
        // 👉  Indispensable pour que le cookie soit envoyé
        'supports_credentials' => true,
    ];
    
    

