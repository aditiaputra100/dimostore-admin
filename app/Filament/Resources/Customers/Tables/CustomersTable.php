<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->imageHeight(40)
                    ->circular()
                    ->default(function (User $record) {
                        dd($record);
                        preg_match_all('/\b\p{L}/u', $record->name, $matches);
                        $initial = implode('', $matches[0]);

                        return "https://ui-avatars.com{$initial}&color=7F9CF5&background=EBF4FF";
                    }),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->copyable(),
                TextColumn::make('phone')
                    ->placeholder('-'),
                TextColumn::make('orders_count')
                    ->label('Order')
                    ->counts('orders'),
                TextColumn::make('orders_sum_total')
                    ->label('Total')
                    ->sum('orders', 'total')
                    ->money('idr', locale: 'id'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->icon(fn (int $state) => match ($state) {
                        1 => Heroicon::CheckCircle,
                        default => Heroicon::XCircle,
                    })
                    ->color(fn (int $state) => match ($state) {
                        1 => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->date(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
                Filter::make('orders_have')
                    ->label('Already shopped')
                    ->query(fn (Builder $query): Builder => $query->whereHas('orders'))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('banned_actived')
                    ->label(fn (User $record) => $record->is_active ? 'Banned' : 'Actived')
                    ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                    ->button()
                    ->modal()
                    ->modalHeading(fn (User $record) => $record->is_active ? 'Ban this account?' : 'Activate this account?')
                    ->modalIcon(fn (User $record) => $record->is_active ? Heroicon::ExclamationTriangle : Heroicon::Check)
                    ->modalDescription(function (User $record) {
                        if ($record->is_active) {
                            return 'Customers will not be able to log in after being blocked.';
                        }

                        return 'Customers will be able to log in again';
                    })
                    ->schema([
                        TextEntry::make('name')
                            ->inlineLabel(),
                        TextEntry::make('email')
                            ->inlineLabel(),
                    ])
                    ->action(function (Action $action, User $record) {
                        $isToBanned = $action->getColor() == 'danger';

                        $record->update([
                            'is_active' => $isToBanned ? 0 : 1,
                        ]);

                        Notification::make()
                            ->title($isToBanned ? 'Customer successfully blocked!' : 'Customer successfully actived!')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
