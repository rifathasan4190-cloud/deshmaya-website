<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
    }

    public function boot(): void
    {
        // $site gives every public view access to business info: $site->get('contact.phone_primary')
        View::composer(['layouts.*', 'partials.*', 'pages.*'], function ($view) {
            $view->with('site', app(SettingService::class));
        });
    }
}
