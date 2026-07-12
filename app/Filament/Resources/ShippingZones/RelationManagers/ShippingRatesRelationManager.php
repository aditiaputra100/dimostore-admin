<?php

namespace App\Filament\Resources\ShippingZones\RelationManagers;

use App\Filament\Resources\ShippingZones\ShippingZoneResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingRatesRelationManager extends RelationManager
{
    protected static string $relationship = 'shippingRates';
    public static ?string $title = 'Rates';

    public function isReadOnly(): bool {
        return false;
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema {
        return $schema
            ->components([
                TextInput::make('min_weight')
                    ->label('Min Weight')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('gram')
                    ->live()
                    ->required(),
                TextInput::make('max_weight')
                    ->label('Max Weight')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('gram')
                    ->rules([
                        fn (Get $get) => 'gt:' . ($get('min_weight') ?? 0)
                    ])
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp.')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->label('Add shipping rate'),
            ])
            ->columns([
                TextColumn::make('min_weight')
                    ->label('Min Weight (gram)')
                    ->numeric(),
                TextColumn::make('max_weight')
                    ->label('Max Weight (gram)')
                    ->numeric(),
                TextColumn::make('price')
                    ->money('idr', locale: 'id'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('min_weight');
    }
}
