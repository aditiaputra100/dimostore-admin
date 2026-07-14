<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class OrderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Orders';
    protected int | string | array $columnSpan = 'full';
    public ?string $filter = 'year';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        switch ($activeFilter) {
            case 'today':
                $start = now()->startOfDay();
                $end = now()->endOfDay();

                $labelFormat = 'H:i';

                $trend = Trend::model(Order::class)
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perHour()
                    ->count();
                    
                break;
            case 'week':
                $start = now()->subDays(6)->startOfDay();
                $end = now()->endOfDay();

                $labelFormat = 'D, d M';

                $trend = Trend::model(Order::class)
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perDay()
                    ->count();

                break;
            case 'month':
                $start = now()->subDays(29)->startOfDay();
                $end = now()->endOfDay();

                $labelFormat = 'd M';

                $trend = Trend::model(Order::class)
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perDay()
                    ->count();

                break;
            default:
                $start = now()->startOfYear();
                $end = now()->endOfYear();

                $labelFormat = 'M Y';

                $trend = Trend::model(Order::class)
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perMonth()
                    ->count();
        }


        // $start = now()->subMonths(11)->startOfMonth();
        // $end = now()->endOfMonth();

        // $orders = Order::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as period')
        //     ->whereBetween('created_at', [
        //         $start->toDateTimeString(), 
        //         $end->toDateTimeString(),
        //     ])
        //     ->groupBy('period')
        //     ->orderBy('period')
        //     ->pluck('count', 'period')
        //     ->toArray();

        // $data = [];
        // $labels = [];
        // $current = $start->copy();

        // while ($current->lessThanOrEqualTo($end)) {
        //     $key = $current->format('Y-m');
        //     $labels[] = $current->translatedFormat('M y');
        //     $data[] = $orders[$key] ?? 0;
        //     $current->addMonth();
        // }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'borderColor' => '#000080',
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate),
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
        return 'line';
    }
}
