<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ✅ Force HTTPS URL generation in production (fixes mixed-content errors behind Railway's proxy)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // ✅ Force Laravel to use a different views path
        config(['view.compiled' => storage_path('framework/views_new')]);
    }

    public function register()
    {
        //
    }
}