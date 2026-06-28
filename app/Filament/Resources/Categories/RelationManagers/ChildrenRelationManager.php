<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Override;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $relatedResource = CategoryResource::class;

    #[Override]
    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('parent')
                    ->label('Parent Category')
                    ->default(fn () => $this->getOwnerRecord()->name)
                    ->disabled()
                    ->required()
                    ->dehydrated(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Sub Categories')
            ->headerActions([
                CreateAction::make()
                    ->label('Create sub category')
                    ->mutateDataUsing(function (array $data): array {
                        $parentCategory = $this->getOwnerRecord();

                        $data['parent_id'] = $parentCategory->id;
                        $data['parent_name'] = $parentCategory->name;

                        return $data;
                    })
                    ->schema([
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
                    ])
                    ->modal()
                    ->modalHeading('Create sub category'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ]);
    }
}
