<?php

namespace App\Filament\Resources\ShippingZones\Pages;

use App\Filament\Resources\ShippingZones\ShippingZoneResource;
use App\Models\ShippingZone;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditShippingZone extends EditRecord
{
    protected static string $resource = ShippingZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, ShippingZone $record) {
                   if ($record->orders()->count() > 0) {
                    Notification::make()
                        ->danger()
                        ->title('Action denied')
                        ->body('Cannot delete a zone that contains orders.')
                        ->send();

                        $action->halt();
                   }
                }),
        ];
    }
}
