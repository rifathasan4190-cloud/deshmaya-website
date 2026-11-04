<?php

namespace App\Filament\Admin\Resources\ContactMessages\Tables;

use App\Filament\Exports\ContactMessageExporter;
use App\Models\ContactMessage;
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

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Received')->dateTime()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('phone')->searchable()->toggleable(),
                TextColumn::make('subject')->searchable()->limit(50),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ContactMessage::STATUS_NEW => 'warning',
                        ContactMessage::STATUS_READ => 'info',
                        ContactMessage::STATUS_REPLIED => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    ContactMessage::STATUS_NEW => 'New',
                    ContactMessage::STATUS_READ => 'Read',
                    ContactMessage::STATUS_REPLIED => 'Replied',
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
                    ->url(fn (ContactMessage $record): string => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: '.$record->subject)),
                Action::make('update_status')
                    ->label('Update status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                ContactMessage::STATUS_NEW => 'New',
                                ContactMessage::STATUS_READ => 'Read',
                                ContactMessage::STATUS_REPLIED => 'Replied',
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn (ContactMessage $record): array => ['status' => $record->status])
                    ->action(function (ContactMessage $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                            'read_at' => $data['status'] === ContactMessage::STATUS_NEW
                                ? null
                                : ($record->read_at ?? now()),
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ContactMessageExporter::class)
                    ->formats([ExportFormat::Csv]),
            ]);
    }
}
