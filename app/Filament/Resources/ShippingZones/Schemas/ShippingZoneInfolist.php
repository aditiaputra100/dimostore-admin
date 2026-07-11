<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ShippingZoneInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('province'),
                IconEntry::make('is_active')
                    ->label('Active')
                    ->icon(fn (int $state): Heroicon => match ($state) {
                        1 => Heroicon::CheckCircle,
                        0 => Heroicon::XCircle,
                    })
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'success',
                        0 => 'danger',
                    }),
            ]);
    }
}
