<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
         if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
    }
        // Password::resetResponse(function ($request, $response) {
        //     return response()->json(['message' => trans($response)]);
        // });

        // Password::brokerResponse(function ($request, $response) {
        //     return response()->json(['message' => trans($response)]);
        // });
    }
}
