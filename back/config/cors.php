
<?php
return [
    'paths' => ['api/*'], // Rutas a las que se aplicará CORS
    'allowed_methods' => ['*'], // Métodos permitidos (GET, POST, etc.)
    'allowed_origins' => ['*'], // Orígenes permitidos
    'allowed_headers' => ['*'], // Cabeceras permitidas
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Habilita el soporte para credenciales
];
