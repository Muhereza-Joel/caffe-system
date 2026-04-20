<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([

                    TextEntry::make('name'),
                    TextEntry::make('description')
                        ->placeholder('-')
                        ->columnSpanFull(),
                    TextEntry::make('price')
                        ->money('Ugx'),
                    TextEntry::make('category.name')
                        ->label('Category'),
                    IconEntry::make('is_available')
                        ->boolean(),
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('updated_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('deleted_at')
                        ->dateTime()
                        ->visible(fn(Product $record): bool => $record->trashed()),
                ])->columnSpanFull(),
            ]);
    }
}
