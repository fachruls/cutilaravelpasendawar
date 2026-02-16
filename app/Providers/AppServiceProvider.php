<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- PENTING!

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // PAKSA HTTPS JIKA DIAKSES LEWAT NGROK
        if($this->app->environment('local') && request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }
    }
}