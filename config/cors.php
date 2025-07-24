<?php
return [
    'paths' => ['api/*'],          // API route တွေမှာ CORS ကို enable လုပ်မယ်။
    'allowed_methods' => ['*'],    // GET, POST, PUT, DELETE အကုန်လုံးခွင့်ပြုမယ်။
    'allowed_origins' => ['*'],    // ဘယ် domain မှာမဆိုခွင့်ပြုမယ်။
    'allowed_headers' => ['*'],    // headers အကုန်လုံးခွင့်ပြုမယ်။
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];