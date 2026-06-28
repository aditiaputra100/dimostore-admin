<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Override;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    #[Override]
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        $record = $this->getRecord();

        $breadcrumbs = [
            $resource::getUrl('index') => $resource::getBreadcrumb(),
        ];

        if ($record->parent) {
            $breadcrumbs[$resource::getUrl('view', ['record' => $record->parent_id])] = $record->parent->name;
        }

        $breadcrumbs[] = $record->name;

        return $breadcrumbs;
    }
}
