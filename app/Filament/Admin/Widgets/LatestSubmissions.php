<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Admin\Resources\QuoteRequests\QuoteRequestResource;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class LatestSubmissions extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.admin.widgets.latest-submissions';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array{ submissions: Collection<int, array<string, mixed>> }
     */
    protected function getViewData(): array
    {
        $contacts = ContactMessage::query()
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get(['id', 'name', 'subject', 'created_at'])
            ->map(fn (ContactMessage $record): array => [
                'type' => 'Contact message',
                'name' => $record->name,
                'detail' => $record->subject,
                'created_at' => $record->created_at,
                'url' => ContactMessageResource::getUrl('view', ['record' => $record]),
            ]);

        $quotes = QuoteRequest::query()
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get(['id', 'name', 'product_name', 'created_at'])
            ->map(fn (QuoteRequest $record): array => [
                'type' => 'Quote request',
                'name' => $record->name,
                'detail' => $record->product_name ?: 'Product not specified',
                'created_at' => $record->created_at,
                'url' => QuoteRequestResource::getUrl('view', ['record' => $record]),
            ]);

        return [
            'submissions' => $contacts
                ->concat($quotes)
                ->sortByDesc('created_at')
                ->take(5)
                ->values(),
        ];
    }
}
