<?php

namespace App\Filament\Admin\Resources\QuoteRequests\Tables;

use App\Filament\Exports\QuoteRequestExporter;
use App\Models\QuoteRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Received')->dateTime()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('company')->searchable()->toggleable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('phone')->searchable()->toggleable(),
                TextColumn::make('country')->searchable()->sortable(),
                TextColumn::make('product_name')->searchable()->label('Product'),
                TextColumn::make('quantity')->numeric(decimalPlaces: 3)->label('Quantity'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        QuoteRequest::STATUS_NEW => 'warning',
                        QuoteRequest::STATUS_REVIEWING => 'info',
                        QuoteRequest::STATUS_QUOTED => 'success',
                        QuoteRequest::STATUS_CLOSED => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    QuoteRequest::STATUS_NEW => 'New',
                    QuoteRequest::STATUS_REVIEWING => 'Reviewing',
                    QuoteRequest::STATUS_QUOTED => 'Quoted',
                    QuoteRequest::STATUS_CLOSED => 'Closed',
                ]),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from')->label('Received from'),
                        DatePicker::make('until')->label('Received until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date))),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                Action::make('reply')
                    ->label('Reply by email')
                    ->url(fn (QuoteRequest $record): string => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: Quote request')),
                Action::make('update_status')
                    ->label('Update status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                QuoteRequest::STATUS_NEW => 'New',
                                QuoteRequest::STATUS_REVIEWING => 'Reviewing',
                                QuoteRequest::STATUS_QUOTED => 'Quoted',
                                QuoteRequest::STATUS_CLOSED => 'Closed',
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn (QuoteRequest $record): array => ['status' => $record->status])
                    ->action(fn (QuoteRequest $record, array $data) => $record->update(['status' => $data['status']])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(QuoteRequestExporter::class)
                    ->formats([ExportFormat::Csv]),
            ]);
    }
}
