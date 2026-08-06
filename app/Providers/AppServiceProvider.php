<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ✅ Force Laravel to use a different views path
        config(['view.compiled' => storage_path('framework/views_new')]);
    }

    public function register()
    {
        //
    }
}