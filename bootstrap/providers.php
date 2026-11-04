<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\SiteServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    SiteServiceProvider::class,
];
