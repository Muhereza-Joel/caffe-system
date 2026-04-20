<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Pending Orders Stat
            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Orders awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            // Today's Payments Stat
            Stat::make("Today's Payments", function () {
                $amount = Payment::whereDate('created_at', Carbon::today())
                    ->where('status', 'completed')
                    ->sum('amount');

                return 'UGX ' . number_format($amount);
            })
                ->description('Total collected today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            // Total Customers Stat
            Stat::make('Total Customers', Customer::count())
                ->description('Total registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
