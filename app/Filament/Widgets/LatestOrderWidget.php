<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\OrderStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use Illuminate\Support\Carbon;

class LatestOrderWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '60s'; 
    public function getTableQuery(): Builder|\Illuminate\Database\Eloquent\Relations\Relation|null {
        return Order::query()->latest()->take(5);
    }
    public function isTablePaginationEnabled(): bool {
        return false;
    }
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number'),
                TextColumn::make('user.name')
                    ->label('Customer'),
                TextColumn::make('total')
                    ->money('idr', locale: 'id'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => OrderStatus::Pending,
                        'info' => OrderStatus::Processing,
                        'primary' => OrderStatus::Shipped,
                        'success' => OrderStatus::Delivered,
                        'danger' => OrderStatus::Canceled,
                    ]),
                TextColumn::make('created_at')
                    ->formatStateUsing(function (Carbon $state): string {
                        if ($state->isToday()) return $state->diffForHumans();

                        if ($state->isYesterday()) return 'Yesterday';

                        return $state->format('d M Y');
                    })
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make('view_order_dashboard')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
