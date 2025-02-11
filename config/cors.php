<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],  // Agregar 'storage/*' para permitir acceder a las imágenes

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173'],  // El origen de tu frontend en Vue.js

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

