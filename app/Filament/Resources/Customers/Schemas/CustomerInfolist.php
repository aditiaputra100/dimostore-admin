<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\User;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->components([
                        Flex::make([
                            ImageEntry::make('avatar')
                                ->hiddenLabel()
                                ->grow(false),
                            Fieldset::make('')
                                ->columns(columns: 1)
                                ->contained(false)
                                ->components([
                                    TextEntry::make('name')
                                        ->inlineLabel(),
                                    TextEntry::make('email')
                                        ->inlineLabel(),
                                    TextEntry::make('phone')
                                        ->placeholder('-')
                                        ->inlineLabel(),
                                    TextEntry::make('created_at')
                                        ->label('Joined since')
                                        ->inlineLabel()
                                        ->dateTime(),
                                ]),
                            TextEntry::make('is_active')
                                ->hiddenLabel()
                                ->formatStateUsing(fn (User $record) => $record->is_active ? 'Active' : 'Block')
                                ->icon(fn (User $record) => $record->is_active ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle)
                                ->iconColor(fn (User $record) => $record->is_active ? 'success' : 'danger')
                                ->weight('bold')
                                ->grow(false),
                        ]),
                        Flex::make([
                            Section::make('orders_amount')
                                ->heading('Number of orders')
                                ->components([
                                    TextEntry::make('orders_count')
                                        ->hiddenLabel()
                                        ->counts('orders')
                                        ->size(TextSize::Large),
                                ]),
                            Section::make('orders_spending')
                                ->heading('Total expenses')
                                ->components([
                                    TextEntry::make('orders_sum_total')
                                        ->hiddenLabel()
                                        ->sum('orders', 'total')
                                        ->money('idr', locale: 'id')
                                        ->size(TextSize::Large),
                                ]),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
