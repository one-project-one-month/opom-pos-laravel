<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Password;

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
        // Password::resetResponse(function ($request, $response) {
        //     return response()->json(['message' => trans($response)]);
        // });

        // Password::brokerResponse(function ($request, $response) {
        //     return response()->json(['message' => trans($response)]);
        // });
    }
}
