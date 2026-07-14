<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\OrderStatus;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Revenue';
    protected int | string | array $columnSpan = 'full';
    public ?string $filter = 'year';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $completeOrders = Order::query()->where('status', OrderStatus::Delivered);

        switch ($activeFilter) {
            case 'today':
                $start = now()->startOfDay();
                $end = now()->endOfDay();

                $labelFormat = 'H:i';

                $trend = Trend::query($completeOrders)
                    ->dateColumn('updated_at')
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perHour()
                    ->sum('total');
                    
                break;
            case 'week':
                $start = now()->subDays(6)->startOfDay();
                $end = now()->endOfDay();

                $labelFormat = 'D, d M';

                $trend = Trend::query($completeOrders)
                    ->dateColumn('updated_at')
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perDay()
                    ->sum('total');

                break;
            case 'month':
                $start = now()->subDays(29)->startOfDay();
                $end = now()->endOfDay();

                $labelFormat = 'd M';

                $trend = Trend::query($completeOrders)
                    ->dateColumn('updated_at')
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perDay()
                    ->sum('total');

                break;
            default:
                $start = now()->startOfYear();
                $end = now()->endOfYear();

                $labelFormat = 'M Y';

                $trend = Trend::query($completeOrders)
                    ->dateColumn('updated_at')
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perMonth()
                    ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'backgroundColor' => '#10b981',
                    'borderRadius' => 4,
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                ]
            ],
            'labels' => $trend->map(fn (TrendValue $value) => Carbon::parse($value->date)->translatedFormat($labelFormat)),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
