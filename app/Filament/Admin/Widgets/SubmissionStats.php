<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubmissionStats extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make(
                'New contact messages',
                ContactMessage::query()->where('status', ContactMessage::STATUS_NEW)->count(),
            )->description('Awaiting review')->color('warning'),
            Stat::make(
                'New quote requests',
                QuoteRequest::query()->where('status', QuoteRequest::STATUS_NEW)->count(),
            )->description('Awaiting review')->color('primary'),
        ];
    }
}
