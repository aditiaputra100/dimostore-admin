<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\OrderStatus;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Update Order')
                ->visible(fn (Order $record) => $record->canBeEdited()),
        ];
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null {
        $title = "Order Detail {$this->record->order_number}";
        $backUrl = static::getResource()::getUrl('index');
        
        return new HtmlString(
            view('filament.orders.custom-header-view', [
                'backUrl' => $backUrl,
                'title' => $title,
            ])->render()
        );
    }
}
