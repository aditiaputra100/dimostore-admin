<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';


    public function table(Table $table): Table
    {
        return $table
            ->headerActions([])
            ->heading('Order Histories')
            ->columns([
                TextColumn::make('order_number'),
                TextColumn::make('total')
                    ->money('idr', locale: 'id'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
