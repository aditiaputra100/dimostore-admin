<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Livewire\Orders\ListOrderItems;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\OrderStatus;
use DB;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Label;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('s_timeline_status')
                    ->key('s-timeline-status')
                    ->headerActions([
                        Action::make('update_status')
                            ->label('Update Status')
                            ->hidden(fn ($record) => in_array($record->status, [OrderStatus::Delivered, OrderStatus::Canceled]))
                            ->modalHeading('Update Order Status')
                            ->schema([
                                TextEntry::make('order_number')
                                    ->inlineLabel(),
                                TextEntry::make('status')
                                    ->label('From')
                                    ->inlineLabel(),
                                TextEntry::make('status_next')
                                    ->state(fn ($record) => $record->status->next())
                                    ->label('To')
                                    ->inlineLabel(),
                                TextInput::make('tracking_number')
                                    ->label('Tracking number (optional)')
                                    ->visible(fn ($record) => $record->status === OrderStatus::Processing)
                                    ->columnSpanFull()
                                    ->belowContent('You can fill this out or change it later on the details page.'),
                                Textarea::make('changelog')
                                    ->label('Status change log (optional)')
                                    ->placeholder('Ex. The package has shipped by JNE')
                                    ->belowContent('If the status is changed to "Shipped," the tracking number can be entered on the details page.')
                                    ->columnSpanFull(),
                            ])
                            ->action(function (array $data, Order $record, Set $set) {
                                $statusNext = $record->status->next()->value;
                                $changeLog = $data['changelog'];
                                $createdBy = auth()->id();
                                $trackingNumber = $data['tracking_number'] ?? null;

                                DB::transaction(function () use ($record, &$statusNext, $changeLog, $createdBy, $trackingNumber) {
    
                                    $record->update([
                                        'status' => $statusNext,
                                        'tracking_number' => $trackingNumber ?? $record->tracking_number,
                                    ]);
    
                                    $record->statusHistories()->create([
                                        'status' => $statusNext,
                                        'note' => $changeLog,
                                        'created_by' => $createdBy,
                                    ]);
                                });

                                $set('status', $statusNext);
                                $set('tracking_number', $trackingNumber);

                                Notification::make('')
                                    ->title("Update order status to $statusNext successfully!")
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->heading('Order Status')
                    ->components([
                        View::make('status_timeline')
                            ->view('filament.orders.status-timeline'),
                        TextInput::make('status')
                            ->label('Current Status')
                            ->disabled()
                            ->inlineLabel(),
                    ])
                    ->columnSpanFull(),
                Section::make('customer')
                    ->heading('Customer Information')
                    ->components([
                        TextInput::make('user_name')
                            ->label('Name')
                            ->formatStateUsing(fn ($record) => $record->user?->name)
                            ->inlineLabel(),
                        TextInput::make('user.email')
                            ->label('Email')
                            ->formatStateUsing(fn ($record) => $record->user?->email)
                            ->inlineLabel(),
                        TextInput::make('user.phone')
                            ->label('Phone')
                            ->formatStateUsing(fn ($record) => $record->user?->phone)
                            ->inlineLabel(),
                    ])
                    ->disabled(),
                Section::make('payment')
                    ->heading('Payment Information')
                    ->components([
                        TextInput::make('payment_method'),
                        TextInput::make('payment_status'),
                        DateTimePicker::make('paid_at')
                            ->placeholder('-'),
                    ])
                    ->inlineLabel()
                    ->disabled(),
                Section::make('shipping_address')
                    ->heading('Shipping Address')
                    ->components([
                        TextInput::make('recipient_name'),
                        TextInput::make('recipient_phone'),
                        Textarea::make('shipping_address'),
                    ])
                    ->inlineLabel()
                    ->disabled(),
                Section::make('shipping_information')
                    ->heading('Shipping Information')
                    ->components([
                        TextInput::make('shipping_zone_id')
                            ->label('Zone')
                            ->formatStateUsing(fn ($record) => $record->shippingZone?->name)
                            ->disabled(),
                        TextInput::make('shipping_cost')
                            ->label('Shipping Cost')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        TextInput::make('tracking_number')
                            ->disabled(fn ($record) => $record->status !== OrderStatus::Shipped)
                            ->placeholder('-')
                            ->belowContent('This field is only active when the status is "Shipped"'),
                    ])
                    ->inlineLabel()
                    ->footerActions([
                        Action::make('update_tracking')
                            ->label('Update Tracking Number')
                            ->visible(fn ($record) => $record->status === OrderStatus::Shipped)
                            ->button()
                            ->action(function (Get $get, Order $record) {
                                $trackingNumber = $get('tracking_number') ?? null;

                                $record->update([
                                    'tracking_number' => $trackingNumber
                                ]);

                                Notification::make('')
                                    ->title('Update tracking number successfully!')
                                    ->body($trackingNumber)
                                    ->success()
                                    ->send();
                            }),
                    ]),
                Section::make('items')
                    ->heading('Order Items')
                    ->components([
                        Livewire::make(ListOrderItems::class)
                            ->columnSpanFull(),
                        TextInput::make('subtotal')
                            ->prefix('Rp'),
                        TextInput::make('shipping_cost')
                            ->prefix('Rp'),
                        Html::make('<div class="flex items-center my-4 text-gray-400 dark:text-gray-500">
                                    <div class="grow border-b border-gray-200 dark:border-gray-700"></div>
                                    <span class="pl-2 font-bold text-lg leading-none">+</span>
                                </div>')
                            ->columnSpanFull(),
                        TextInput::make('total')
                            ->prefix('Rp'),
                    ])
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->disabled(),
                Section::make('note_customer')
                    ->heading('Notes From Customer')
                    ->components([
                        Textarea::make('notes')
                        ->hiddenLabel()
                        ->placeholder('No customer notes'),
                    ])
                    ->disabled(),
                Section::make('note_admin')
                    ->heading('Admin Notes')
                    ->schema([
                        Textarea::make('admin_notes')
                        ->hiddenLabel()
                        ->placeholder('Write a notes...')
                        ->live(onBlur: false),
                    ])
                    ->footerActions([
                        Action::make('save')
                            ->label('Save Notes')
                            ->action(function (Model $record, Get $get) {
                                $adminNotes = $get('admin_notes');

                                $record->admin_notes = $adminNotes;
                                $record->save();

                                Notification::make()
                                    ->title('Update notes successfully!')
                                    ->success()
                                    ->send();
                            }),
                    ]),
                Section::make('history')
                    ->heading('Order Status History')
                    ->components([
                        View::make('history_timeline')
                            ->view('filament.orders.history-timeline'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
