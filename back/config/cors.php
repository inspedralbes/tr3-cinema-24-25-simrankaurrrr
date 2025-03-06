<?php
return [
    'supports_credentials' => true,

    'allowed_origins' => [
        '*',  // Dirección de tu frontend
        // Añade más orígenes si es necesario
    ],

    'allowed_methods' => ['*'],  // Permite todos los métodos HTTP (GET, POST, PUT, DELETE, etc.)
    'allowed_headers' => ['*'],  // Permite todos los encabezados

    'exposed_headers' => [],
    'max_age' => 0,
];
