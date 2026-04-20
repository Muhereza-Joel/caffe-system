<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->schema([

                        Grid::make()->schema([
                            Hidden::make('user_id')
                                ->default(auth()->id()),

                            Select::make('customer_id')
                                ->relationship('customer', 'name'),

                            Select::make('table_id')
                                ->relationship('table', 'name'),

                        ])->columns(2),
                        Grid::make()->schema([
                            Select::make('status')
                                ->required()
                                ->options([
                                    'pending' => 'Pending',
                                    'processing' => 'Processing',
                                    // 'completed' => 'Completed',
                                ])
                                ->default('pending'),

                            TextInput::make('total_amount')
                                ->required()
                                ->numeric()
                                ->readOnly()
                                ->default(0),
                        ])->columns(2),

                    ])->columnSpanFull(),
            ]);
    }
}
