<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    public function form(Schema $schema): Schema
    {
        $updateAmount = function ($get, $set) {
            $quantity = (float)($get('quantity') ?? 0);
            $price = (float)($get('price') ?? 0);
            $set('amount', $quantity * $price);
        };

        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) use ($updateAmount) {
                        $product = \App\Models\Product::find($state);
                        $set('price', $product?->price ?? 0);
                        // Store labels in hidden states to avoid re-querying DB in every suffix closure
                        $set('unit_singular', $product?->singular_label);
                        $set('unit_plural', $product?->plural_label);

                        $updateAmount($get, $set);
                    }),

                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->live()
                    ->afterStateUpdated($updateAmount)
                    ->suffix(fn($get) => ' / ' . ($get('quantity') == 1 ? $get('unit_singular') : $get('unit_plural'))),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('UGX ')
                    ->live()
                    ->afterStateUpdated($updateAmount)
                    ->suffix(fn($get) => ' per ' . ($get('quantity') == 1 ? $get('unit_singular') : $get('unit_plural'))),

                TextInput::make('amount')
                    ->disabled()
                    ->numeric()
                    ->prefix('UGX ')
                    ->dehydrated(false)
                    ->afterStateHydrated(fn($set, $get) => $updateAmount($get, $set))
                    ->suffix(fn($get) => ' ' . ($get('quantity') == 1 ? $get('unit_singular') : $get('unit_plural'))),

                // Hidden fields to hold your labels so suffixes stay fast
                Hidden::make('unit_singular'),
                Hidden::make('unit_plural'),
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item')
            ->columns([
                TextColumn::make('product.name')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->suffix(
                        fn($record) => $record->quantity == 1
                            ? ' ' . $record->product?->singular_label
                            : ' ' . $record->product?->plural_label
                    )
                    ->sortable(),
                TextColumn::make('price')
                    ->money('Ugx')
                    ->suffix(fn($record) => ' per ' . $record->product?->singular_label)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
