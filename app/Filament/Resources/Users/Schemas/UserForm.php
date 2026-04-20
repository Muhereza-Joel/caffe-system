<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // BASIC INFORMATION
                Section::make('Basic Information')
                    ->description('User personal details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Select::make('roles')
                                    ->label('Role / Privilege')
                                    ->relationship(
                                        name: 'roles',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn($query) =>
                                        $query->where('name', '!=', 'super_admin')
                                    )
                                    ->preload()
                                    ->searchable()
                                    ->multiple(false)
                                    ->required(),
                            ]),
                    ])->columnSpanFull(),

                // SECURITY
                Section::make('Security')
                    ->description('Manage authentication credentials')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->maxLength(161)
                                    ->placeholder('Enter a secure password')
                                    ->helperText(fn() => $schema->getOperation() !== 'view' ? 'Only required when creating a user. Leave blank to keep the current password.' : null)
                                    ->dehydrated(fn($state) => filled($state)),
                            ]),
                    ])->columnSpanFull(),

            ]);
    }
}
