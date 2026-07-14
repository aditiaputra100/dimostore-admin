<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('banned_actived')
                ->label(fn (User $record) => $record->is_active ? 'Banned' : 'Actived')
                ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
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
        ];
    }
}
