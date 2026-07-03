<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('order_number')
                    ->required(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'canceled' => 'Canceled',
        ])
                    ->required(),
                Select::make('payment_method')
                    ->options(['bank_transfer' => 'Bank transfer', 'qris' => 'Qris', 'cod' => 'Cod'])
                    ->required(),
                Select::make('payment_status')
                    ->options(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'refunded' => 'Refunded'])
                    ->required(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                Select::make('shipping_zone_id')
                    ->relationship('shippingZone', 'name')
                    ->default(null),
                TextInput::make('recipient_name')
                    ->required(),
                TextInput::make('recipient_phone')
                    ->tel()
                    ->required(),
                Textarea::make('shipping_address')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('tracking_number')
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
