<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestOrderWidget;
use App\Filament\Widgets\OrderChartWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    public function getWidgets(): array {
        return [
            StatsOverviewWidget::class,
            LatestOrderWidget::class,
            OrderChartWidget::class,
            RevenueChartWidget::class,
        ];
    }
}
