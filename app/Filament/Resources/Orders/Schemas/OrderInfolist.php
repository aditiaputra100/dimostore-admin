<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('order_number'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('payment_method')
                    ->badge(),
                TextEntry::make('payment_status')
                    ->badge(),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('shipping_cost')
                    ->money(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('shippingZone.name')
                    ->label('Shipping zone')
                    ->placeholder('-'),
                TextEntry::make('recipient_name'),
                TextEntry::make('recipient_phone'),
                TextEntry::make('shipping_address')
                    ->columnSpanFull(),
                TextEntry::make('tracking_number')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('admin_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
