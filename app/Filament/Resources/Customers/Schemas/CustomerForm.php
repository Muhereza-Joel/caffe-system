<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->description('Enter the customer’s basic details below.')
                    ->schema([

                        Grid::make()
                            ->schema([
                                Select::make('salutation')
                                    ->label('Salutation')
                                    ->options([
                                        'mr' => 'Mr.',
                                        'mrs' => 'Mrs.',
                                        'miss' => 'Miss',
                                        'dr' => 'Dr.',
                                        'prof' => 'Prof.',
                                    ])
                                    ->placeholder('Select salutation')
                                    ->helperText('Choose the appropriate title for the customer.')
                                    ->required(),

                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->placeholder('Enter full name')
                                    ->helperText('Provide the customer’s official name.')
                                    ->required(),
                            ])->columns(2),

                        Grid::make()
                            ->schema([

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->placeholder('e.g. +256 7XXXXXXXX')
                                    ->helperText('Enter an active phone number.'),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->placeholder('example@email.com')
                                    ->helperText('Provide a valid email address for communication.'),
                            ])->columns(2),


                    ])
                    ->columnSpanFull(),
            ]);
    }
}
