<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShippingZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(100)
                    ->required(),
                TextInput::make('province')
                    ->maxLength(100)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Zone Active')
                    ->default(1),
            ]);
    }
}
