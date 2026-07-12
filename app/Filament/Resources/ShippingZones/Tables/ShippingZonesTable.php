<?php

namespace App\Filament\Resources\ShippingZones\Tables;

use App\Filament\Resources\ShippingZones\ShippingZoneResource;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ShippingZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('province')
                    ->searchable(),
                TextColumn::make('shipping_rates_count')
                    ->label('Total Rates')
                    ->counts('shippingRates'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->icon(fn (int $state): Heroicon => match ($state) {
                        1 => Heroicon::CheckCircle,
                        0 => Heroicon::XCircle,
                    })
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'success',
                        0 => 'danger',
                    }),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(fn (DeleteAction $action, ShippingZone $record) => ShippingZoneResource::validateDeletion($action, $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, Collection $records) {
                            foreach ($records as $record) {
                                ShippingZoneResource::validateDeletion($action, $record);
                            }
                        }),
                ]),
            ])
            ->defaultSort('name');
    }
}
