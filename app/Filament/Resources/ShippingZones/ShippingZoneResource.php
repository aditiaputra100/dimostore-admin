<?php

namespace App\Filament\Resources\ShippingZones;

use App\Filament\Resources\ShippingZones\Pages\CreateShippingZone;
use App\Filament\Resources\ShippingZones\Pages\EditShippingZone;
use App\Filament\Resources\ShippingZones\Pages\ListShippingZones;
use App\Filament\Resources\ShippingZones\Pages\ViewShippingZone;
use App\Filament\Resources\ShippingZones\RelationManagers\ShippingRatesRelationManager;
use App\Filament\Resources\ShippingZones\Schemas\ShippingZoneForm;
use App\Filament\Resources\ShippingZones\Schemas\ShippingZoneInfolist;
use App\Filament\Resources\ShippingZones\Tables\ShippingZonesTable;
use App\Models\ShippingZone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShippingZoneResource extends Resource
{
    protected static ?string $model = ShippingZone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::GlobeAlt;
    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return ShippingZoneForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShippingZoneInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingZonesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ShippingRatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingZones::route('/'),
            'create' => CreateShippingZone::route('/create'),
            'view' => ViewShippingZone::route('/{record}'),
            'edit' => EditShippingZone::route('/{record}/edit'),
        ];
    }
}
