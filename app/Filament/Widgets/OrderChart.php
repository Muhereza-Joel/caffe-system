<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class OrderChart extends ChartWidget
{
    protected ?string $heading = 'Order Trends';
    protected string $color = 'info';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Trend::model(Order::class)
            ->between(
                start: now()->startOfWeek(), // Monday 00:00
                end: now()->endOfWeek(),   // Sunday 23:59
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders This Week',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('D')), // e.g., Mon, Tue
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
