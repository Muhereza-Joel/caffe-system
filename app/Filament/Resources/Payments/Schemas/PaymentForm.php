<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->schema([
                        Grid::make()->schema([
                            Select::make('order_id')
                                ->relationship(
                                    name: 'order',
                                    titleAttribute: 'id',
                                    modifyQueryUsing: fn($query) => $query->whereIn('status', ['pending', 'processing', 'partially_paid'])
                                )
                                ->required()
                                ->live()
                                ->getOptionLabelFromRecordUsing(
                                    fn($record) => ($record->customer?->name ?? '-') . ' @ ' . ($record->table?->name ?? '-') .
                                        ' — ' . $record->created_at?->format('Y-m-d')
                                )
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search) {
                                    return \App\Models\Order::query()
                                        ->whereIn('status', ['pending', 'processing'])
                                        ->where(function ($query) use ($search) {
                                            $query->where('id', 'like', "%{$search}%")
                                                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                                                ->orWhereHas('table', fn($q) => $q->where('name', 'like', "%{$search}%"));
                                        })
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn($order) => [
                                            $order->id => ($order->customer?->name ?? '-') . ' @ ' . ($order->table?->name ?? '-') .
                                                ' — ' . $order->created_at?->format('Y-m-d')
                                        ]);
                                })
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state) {
                                        $order = \App\Models\Order::find($state);
                                        $balance = ($order?->total_amount ?? 0) - ($order?->paid_amount ?? 0);

                                        $set('remaining_balance', $balance);
                                        $set('amount', null); // Keep empty for manual entry
                                    }
                                })
                                ->preload(),

                            TextInput::make('remaining_balance')
                                ->label('Remaining Balance')
                                ->prefix('UGX ')
                                ->readOnly()
                                ->numeric()
                                ->dehydrated(false)
                                ->afterStateHydrated(function ($set, $get) {
                                    if ($orderId = $get('order_id')) {
                                        $order = \App\Models\Order::find($orderId);
                                        $balance = ($order?->total_amount ?? 0) - ($order?->paid_amount ?? 0);
                                        $set('remaining_balance', $balance);
                                    }
                                }),

                            TextInput::make('amount')
                                ->label('Amount to Pay')
                                ->prefix('UGX ')
                                ->required()
                                ->numeric()
                                ->live()
                                ->maxValue(fn($get) => (float) $get('remaining_balance'))
                                ->hint(fn($get) => $get('remaining_balance') ? 'Balance: UGX ' . number_format($get('remaining_balance')) : null)
                                ->hintColor('danger'),

                        ])->columns(2),

                        Grid::make()->schema([
                            Select::make('method')
                                ->options([
                                    'cash' => 'Cash',
                                    'mobile_money' => 'Mobile Money'
                                ])
                                ->required(),

                            Select::make('status')
                                ->required()
                                ->options([
                                    'pending' => 'Pending',
                                    'completed' => 'Completed'
                                ])
                                ->default('completed'),

                            TextInput::make('transaction_reference')
                                ->readOnly()
                                ->default(fn() => (string) Str::uuid()),
                        ])->columns(2),

                    ])->columnSpanFull(),
            ]);
    }
}
