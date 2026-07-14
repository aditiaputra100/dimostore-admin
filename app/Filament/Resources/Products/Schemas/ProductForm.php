<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic information')
                    ->schema([
                        TextInput::make('name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function(Set $set, ?string $state, string $operation) {

                                $set('slug', Str::slug($state));
                            })
                            ->maxLength(200)
                            ->required(),
                        TextInput::make('slug')
                            ->readOnly()
                            ->unique()
                            ->required(),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(50)
                            ->required(),
                        Textarea::make('description')
                            ->default(null)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Price')
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),
                        TextInput::make('original_price')
                            ->numeric()
                            ->minValue(0)
                            ->default(null)
                            ->prefix('$'),
                    ]),
                Section::make('Inventory')
                    ->schema([
                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        TextInput::make('weight')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                    ]),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Radio::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive'])
                    ->inline()
                    ->required(),
                FileUpload::make('main_image')
                    ->image()
                    ->disk('public')
                    ->directory('products/main')
                    ->columnSpanFull(),
                FileUpload::make('images')
                    ->label('Gallery')
                    ->multiple()
                    ->disk('public')
                    ->directory('products/gallery')
                    ->maxFiles(5)
                    ->image()
                    ->reorderable()
                    ->appendFiles()
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ]);
    }
}
