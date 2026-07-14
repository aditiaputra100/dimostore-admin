<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void {
        $gallery = $this->data['images'] ?? [];

        foreach ($gallery as $index => $image) {
            $this->record->images()->create([
                'image_path' => $image,
                'sort_order' => $index
            ]);
        }
    }
}
