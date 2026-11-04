<?php

namespace App\Filament\Exports;

use App\Models\QuoteRequest;
use App\Support\CsvValue;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class QuoteRequestExporter extends Exporter
{
    protected static ?string $model = QuoteRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('company')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('email')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('phone')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('country')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('product_id'),
            ExportColumn::make('product_name')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('quantity'),
            ExportColumn::make('unit')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('message')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('status')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('ip')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('user_agent')->formatStateUsing(fn (mixed $state): mixed => CsvValue::neutralize($state)),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your quote request export has completed and '.Str::of('row')->counted($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Str::of('row')->counted($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
