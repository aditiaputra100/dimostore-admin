<?php

namespace App\Livewire\Orders;

use App\Models\OrderItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ListOrderItems extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => OrderItem::query()->where('order_id', $this->record?->id))
            ->columns([
                TextColumn::make('product_name'),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('idr', locale: 'id')
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->money('idr', locale: 'id')
                    ->sortable(),
            ]);
    }

    public function render(): View
    {
        return view('filament.orders.list-order-items');
    }
}
