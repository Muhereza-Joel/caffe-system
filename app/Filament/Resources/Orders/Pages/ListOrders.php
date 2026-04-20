<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Orders'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->icon('heroicon-m-clock'),

            'processing' => Tab::make('Processing')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'processing'))
                ->icon('heroicon-m-arrow-path'),

            'partially_paid' => Tab::make('Partially Paid')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'partially_paid'))
                ->icon('heroicon-m-banknotes')
                ->badge(fn() => $this->getModel()::where('status', 'partially_paid')->count()),

            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->icon('heroicon-m-check-badge'),

            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->icon('heroicon-m-x-circle'),
        ];
    }

    // Optional: Set a default tab
    public function getDefaultActiveTab(): string | int | null
    {
        return 'processing';
    }
}
