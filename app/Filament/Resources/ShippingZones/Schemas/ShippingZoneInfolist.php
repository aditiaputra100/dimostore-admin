<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShippingZoneInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('province'),
                TextEntry::make('is_active')
                    ->numeric(),
            ]);
    }
}
