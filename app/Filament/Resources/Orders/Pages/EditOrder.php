<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\OrderStatus;
use DB;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            Action::make('cancel_btn')
                ->label('Cancel Order')
                ->color('danger')
                ->button()
                ->modalHeading('Cancer Order')
                ->visible(fn (Order $record) => !($record->status === OrderStatus::Delivered || $record->status === OrderStatus::Canceled))
                ->schema([
                    TextEntry::make('order_number')
                        ->label('Order number')
                        ->inlineLabel()
                        ->belowLabel('This action cannot be undone.'),
                    Textarea::make('changelog')
                        ->label('Reason for cancellation (optional)')
                        ->placeholder('Ex. The stock is empty'),
                ])
                ->action(function (array $data, Order $record) {
                    $note = $data['changelog'] ?? null;

                    DB::transaction(function () use ($note, $record) {
                        $record->update([
                            'status' => OrderStatus::Canceled,
                        ]);
    
                        $record->statusHistories()->create([
                            'status' => OrderStatus::Canceled,
                            'note' => $note,
                            'created_by' => auth()->id(),
                        ]);
                    });

                    Notification::make()
                        ->title("Cancel order $record->id is successfully")
                        ->body($note)
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getFormActions(): array {
        return [];
    }
}
