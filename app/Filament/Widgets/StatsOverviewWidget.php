<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\OrderStatus;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?string $poolingInterval = '60s';
    protected function getStats(): array
    {
        $activeProducts = Product::where('status', 'active')->count();
        $totalOrders = Order::count();
        $revenue = Order::where('status', OrderStatus::Delivered)->sum('total');
        $totalCustomers = User::where('is_active', true)->count();

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formattedMoney = $formatter->formatCurrency($revenue, 'IDR');

        return [
            Stat::make('Active Products', $activeProducts),
            Stat::make('Total Orders', $totalOrders)
                ->icon('heroicon-m-shopping-cart'),
            Stat::make('Revenue', $formattedMoney)
                ->icon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('Customers', $totalCustomers)
                ->icon('heroicon-m-user'),
        ];
    }
}
