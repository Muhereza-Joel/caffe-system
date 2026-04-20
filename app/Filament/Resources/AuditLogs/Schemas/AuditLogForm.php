<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('actor_user_id')
                    ->numeric(),
                TextInput::make('entity_table')
                    ->required(),
                TextInput::make('entity_id'),
                TextInput::make('action')
                    ->required(),
                Textarea::make('changes_json')
                    ->columnSpanFull(),
                TextInput::make('ip_address'),
            ]);
    }
}
