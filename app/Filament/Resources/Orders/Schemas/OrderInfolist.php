<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Livewire\Orders\ListOrderItems;
use App\OrderStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->heading('Order Status')
                    ->components([
                        ViewEntry::make('status_timeline')
                            ->view('filament.orders.status-timeline'),
                        TextEntry::make('status')
                            ->label('Current Status')
                            ->inlineLabel()
                            ->weight('bold'),
                    ])
                    ->columnSpanFull(),
                Section::make('customer')
                    ->heading('Customer Information')
                    ->components([
                        TextEntry::make('user.name')
                            ->label('Name')
                            ->inlineLabel(),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->inlineLabel(),
                        TextEntry::make('user.phone')
                            ->label('Phone')
                            ->inlineLabel(),
                    ]),
                Section::make('payment')
                    ->heading('Payment Information')
                    ->components([
                        TextEntry::make('payment_method'),
                        TextEntry::make('payment_status')
                            ->badge(),
                        TextEntry::make('paid_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->inlineLabel(), 
                Section::make('shipping_address')
                    ->heading('Shipping Address')
                    ->components([
                        TextEntry::make('recipient_name')
                            ->inlineLabel(),
                        TextEntry::make('recipient_phone')
                            ->inlineLabel(),
                        TextEntry::make('shipping_address')
                            ->columnSpanFull()
                            ->inlineLabel(),
                    ])
                    ->extraAttributes([
                        'class' => 'h-full'
                    ]),
                Section::make('shipping_information')
                    ->heading('Shipping Information')
                    ->components([
                        TextEntry::make('shippingZone.name')
                            ->label('Zone'),
                        TextEntry::make('shipping_cost')
                            ->label('Shipping cost')
                            ->money('idr', locale: 'id'),
                        TextEntry::make('tracking_number')
                            ->placeholder('-')
                            ->belowContent('This field is only active when the status is "Shipped"'),

                    ])
                    ->inlineLabel()
                    ->extraAttributes([
                        'class' => 'h-full'
                    ]),
                Section::make('items')
                    ->heading('Order Items')
                    ->schema([
                        Livewire::make(ListOrderItems::class)
                            ->columnSpanFull(),
                        TextEntry::make('subtotal')
                            ->money('idr', locale: 'id')
                            ->alignRight(),
                        TextEntry::make('shipping_cost')
                            ->money('idr', locale: 'id')
                            ->alignRight(),
                        Html::make('<div class="flex items-center my-4 text-gray-400 dark:text-gray-500">
                                    <div class="grow border-b border-gray-200 dark:border-gray-700"></div>
                                    <span class="pl-2 font-bold text-lg leading-none">+</span>
                                </div>')
                            ->columnSpanFull(),
                        TextEntry::make('total')
                            ->money('idr', locale: 'id')
                            ->weight('bold')
                            ->alignRight(),
                    ])
                    ->inlineLabel()
                    ->columnSpanFull(),
                Section::make('note_customer')
                    ->heading('Notes From Customer')
                    ->components([
                        TextEntry::make('notes')
                            ->hiddenLabel()
                            ->placeholder('No customer notes')
                            ->columnSpanFull(),
                    ]),
                Section::make('note_admin')
                    ->heading('Admin Notes')
                    ->components([
                        TextEntry::make('admin_notes')
                            ->hiddenLabel()
                            ->placeholder('-')
                            ->live(onBlur: false)
                            ->columnSpanFull(),
                    ]),
                Section::make('history')
                    ->heading('Order Status History')
                    ->components([
                        ViewEntry::make('history_timeline')
                            ->view('filament.orders.history-timeline'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
