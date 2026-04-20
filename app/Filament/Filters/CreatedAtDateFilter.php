<?php

namespace App\Filament\Filters;

use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class CreatedAtDateFilter
{
    public static function make(): Filter
    {
        return Filter::make('created_at')
            ->label('Created Between')
            ->form([
                \Filament\Forms\Components\Select::make('preset')
                    ->label('Quick Range')
                    ->options([
                        'today'        => 'Today',
                        'yesterday'    => 'Yesterday',
                        '7_days'       => 'Last 7 Days',
                        '14_days'      => 'Last 14 Days',
                        '30_days'      => 'Last 30 Days',
                        '90_days'      => 'Last 90 Days',
                        'this_week'    => 'This Week (Mon–Today)',
                        'last_week'    => 'Last Week (Mon–Sun)',
                        'this_month'   => 'This Month',
                        'last_month'   => 'Last Month',
                        'custom_month' => 'Custom Month',
                        'this_quarter' => 'This Quarter',
                        'last_quarter' => 'Last Quarter',
                        'this_year'    => 'This Year',
                        'last_year'    => 'Last Year',
                        'custom_week'  => 'Custom Week',
                    ]),
                \Filament\Forms\Components\TextInput::make('month_number')
                    ->label('Month (1–12)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12)
                    ->visible(fn($get) => $get('preset') === 'custom_month'),
                \Filament\Forms\Components\TextInput::make('month_year')
                    ->label('Year')
                    ->numeric()
                    ->default(now()->year)
                    ->visible(fn($get) => $get('preset') === 'custom_month'),
                \Filament\Forms\Components\TextInput::make('week_number')
                    ->label('Week Number')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(53)
                    ->visible(fn($get) => $get('preset') === 'custom_week'),
                \Filament\Forms\Components\TextInput::make('week_year')
                    ->label('Year')
                    ->numeric()
                    ->default(now()->year)
                    ->visible(fn($get) => $get('preset') === 'custom_week'),
                \Filament\Forms\Components\DateTimePicker::make('from')
                    ->label('Added From')
                    ->native(false)
                    ->placeholder('Start Date & Time'),
                \Filament\Forms\Components\DateTimePicker::make('until')
                    ->label('Added Until')
                    ->native(false)
                    ->placeholder('End Date & Time'),
            ])
            ->query(function (Builder $query, array $data) {
                $table = $query->getModel()->getTable();

                if (! empty($data['preset'])) {
                    return match ($data['preset']) {
                        'today'        => $query->whereDate("$table.created_at", now()->toDateString()),
                        'yesterday'    => $query->whereDate("$table.created_at", now()->subDay()->toDateString()),
                        '7_days'       => $query->whereDate("$table.created_at", '>=', now()->subDays(7)),
                        '14_days'      => $query->whereDate("$table.created_at", '>=', now()->subDays(14)),
                        '30_days'      => $query->whereDate("$table.created_at", '>=', now()->subDays(30)),
                        '90_days'      => $query->whereDate("$table.created_at", '>=', now()->subDays(90)),
                        'this_week'    => $query->whereBetween("$table.created_at", [
                            now()->startOfWeek(),
                            now(),
                        ]),
                        'last_week'    => $query->whereBetween("$table.created_at", [
                            now()->startOfWeek()->subWeek(),
                            now()->endOfWeek()->subWeek(),
                        ]),
                        'this_month'   => $query->whereMonth("$table.created_at", now()->month)
                            ->whereYear("$table.created_at", now()->year),
                        'last_month'   => $query->whereMonth("$table.created_at", now()->subMonth()->month)
                            ->whereYear("$table.created_at", now()->subMonth()->year),
                        'custom_month' => $query->when(
                            ! empty($data['month_number']) && ! empty($data['month_year']),
                            function ($q) use ($table, $data) {
                                $start = Carbon::create($data['month_year'], $data['month_number'], 1)->startOfMonth();
                                $end   = Carbon::create($data['month_year'], $data['month_number'], 1)->endOfMonth();
                                $q->whereBetween("$table.created_at", [$start, $end]);
                            }
                        ),
                        'this_quarter' => $query->whereRaw("EXTRACT(QUARTER FROM $table.created_at) = ?", [now()->quarter])
                            ->whereYear("$table.created_at", now()->year),
                        'last_quarter' => $query->whereRaw("EXTRACT(QUARTER FROM $table.created_at) = ?", [now()->subQuarter()->quarter])
                            ->whereYear("$table.created_at", now()->subQuarter()->year),
                        'this_year'    => $query->whereYear("$table.created_at", now()->year),
                        'last_year'    => $query->whereYear("$table.created_at", now()->subYear()->year),
                        'custom_week'  => $query->when(
                            ! empty($data['week_number']) && ! empty($data['week_year']),
                            function ($q) use ($table, $data) {
                                $start = Carbon::now()->setISODate($data['week_year'], $data['week_number'])->startOfWeek();
                                $end   = Carbon::now()->setISODate($data['week_year'], $data['week_number'])->endOfWeek();
                                $q->whereBetween("$table.created_at", [$start, $end]);
                            }
                        ),
                        default        => $query,
                    };
                }

                return $query
                    ->when($data['from'] ?? null, fn($q, $date) => $q->where("$table.created_at", '>=', $date))
                    ->when($data['until'] ?? null, fn($q, $date) => $q->where("$table.created_at", '<=', $date));
            })
            ->indicateUsing(function (array $data): ?string {
                if (! empty($data['preset'])) {
                    return match ($data['preset']) {
                        'today'        => 'Created Today',
                        'yesterday'    => 'Created Yesterday',
                        '7_days'       => 'Created in Last 7 Days',
                        '14_days'      => 'Created in Last 14 Days',
                        '30_days'      => 'Created in Last 30 Days',
                        '90_days'      => 'Created in Last 90 Days',
                        'this_week'    => 'Created This Week (Mon–Today)',
                        'last_week'    => 'Created Last Week (Mon–Sun)',
                        'this_month'   => 'Created This Month',
                        'last_month'   => 'Created Last Month',
                        'custom_month' => ! empty($data['month_number']) && ! empty($data['month_year'])
                            ? "Created in {$data['month_year']}-{$data['month_number']}"
                            : 'Created in Custom Month',
                        'this_quarter' => 'Created This Quarter',
                        'last_quarter' => 'Created Last Quarter',
                        'this_year'    => 'Created This Year',
                        'last_year'    => 'Created Last Year',
                        'custom_week'  => ! empty($data['week_number']) && ! empty($data['week_year'])
                            ? "Created in Week {$data['week_number']} of {$data['week_year']}"
                            : 'Created in Custom Week',
                        default        => null,
                    };
                }

                if (! empty($data['from']) && ! empty($data['until'])) {
                    return "Created between {$data['from']} and {$data['until']}";
                }

                if (! empty($data['from'])) {
                    return "Created after {$data['from']}";
                }

                if (! empty($data['until'])) {
                    return "Created before {$data['until']}";
                }

                return null;
            });
    }
}
