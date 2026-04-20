<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // BASIC INFORMATION
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Full Name'),

                                TextEntry::make('email')
                                    ->label('Email address'),
                                TextEntry::make('roles.name')
                                    ->label('Previllages'),
                            ]),
                    ]),

                // VERIFICATION
                Section::make('Verification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),

                // SYSTEM INFORMATION
                Section::make('System Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),

            ]);
    }
}
