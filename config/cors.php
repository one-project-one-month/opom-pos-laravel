<?php
return [


    'paths' => ['api/*', 'login', 'sanctum/csrf-cookie', 'v1/*'], 

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:3000', 'https://pos.kyawmgmglwin.site', 'https://www.pos.kyawmgmglwin.site'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], 

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

