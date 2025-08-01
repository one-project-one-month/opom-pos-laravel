<?php
// return [
//        // API route တွေမှာ CORS ကို enable လုပ်မယ်။
//     'allowed_methods' => ['*'],    // GET, POST, PUT, DELETE အကုန်လုံးခွင့်ပြုမယ်။
//     'allowed_origins' => ['*'],    // ဘယ် domain မှာမဆိုခွင့်ပြုမယ်။
//     'allowed_headers' => ['*'],    // headers အကုန်လုံးခွင့်ပြုမယ်။
//     'exposed_headers' => [],
//     'max_age' => 0,
//     'supports_credentials' => false,
// ];
return [


    'paths' => ['api/*', 'login', 'sanctum/csrf-cookie', 'v1/*'], // make sure 'login' or your API path is here

    'allowed_methods' => ['*'], // or ['GET', 'POST', 'OPTIONS', 'PUT', 'DELETE']

    'allowed_origins' => ['http://localhost:3000'], // OR '*' only for dev

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // for Content-Type, Authorization, etc

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // important if using cookies / auth headers

];

