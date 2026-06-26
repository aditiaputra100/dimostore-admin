<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->default(null),
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function(Set $set, ?string $state, string $operation) {

                        $set('slug', Str::slug($state));
                    })
                    ->maxLength(255)
                    ->required(),
                TextInput::make('slug')
                    ->unique()
                    ->required()
                    ->readOnly(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                Checkbox::make('is_active')
                    ->label('Active Status')
                    ->default(true),
            ]);
    }
}
