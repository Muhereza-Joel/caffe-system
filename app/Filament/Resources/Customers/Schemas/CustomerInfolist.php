<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Customer;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Customer Basic Information")
                    ->schema([
                        Grid::make()->schema([
                            TextEntry::make('salutation'),
                            TextEntry::make('name'),
                            TextEntry::make('phone')
                                ->placeholder('-'),
                            TextEntry::make('email')
                                ->label('Email address')
                                ->placeholder('-'),

                        ])->columns(2),

                    ])->columnSpanFull(),

                Section::make("System Information")
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('deleted_at')
                                    ->dateTime()
                                    ->visible(fn(Customer $record): bool => $record->trashed()),

                            ]),

                    ])->columnSpanFull(),
            ]);
    }
}
