<?php

namespace App\Filament\Resources\Orders\Tables;

use App\OrderStatus;
use App\PaymentMethod;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order Number')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Customer Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->money('idr', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => OrderStatus::Pending,
                        'info' => OrderStatus::Processing,
                        'primary' => OrderStatus::Shipped,
                        'success' => OrderStatus::Delivered,
                        'danger' => OrderStatus::Canceled,
                    ]),
                TextColumn::make('payment_method'),
                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(OrderStatus::class),
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->multiple()
                    ->options(PaymentMethod::class),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Start Date'),
                        DatePicker::make('created_until')
                            ->label('End Date')
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }
                        
                        return $indicators;
                        
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
