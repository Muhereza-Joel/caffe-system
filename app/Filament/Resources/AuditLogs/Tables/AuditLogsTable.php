<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Filament\Filters\CreatedAtDateFilter;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->searchable(),
                TextColumn::make('actor.name')
                    ->numeric()
                    ->searchable(),
                TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->colors(['danger' => 'critical', 'warning' => 'warning', 'success' => 'info',])
                    ->searchable(),
                TextColumn::make('entity_table')
                    ->searchable(),
                TextColumn::make('entity_id')
                    ->numeric()
                    ->searchable(),
                TextColumn::make('action')
                    ->searchable(),
                TextColumn::make('ip_address'),

                TextColumn::make('user_agent')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('http_method')
                    ->searchable(),
                TextColumn::make('correlation_id')
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
                CreatedAtDateFilter::make(),
                SelectFilter::make('actor_user_id')
                    ->label('Performed By')
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),

                SelectFilter::make('entity_table')
                    ->label('Resource / Table')
                    ->options(function () {
                        // Dynamically get unique tables from the audit log
                        return \App\Models\AuditLog::distinct()
                            ->pluck('entity_table', 'entity_table')
                            ->toArray();
                    }),

                SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                        'restore' => 'Restore',
                        'login' => 'Login',
                    ]),
                TrashedFilter::make(),
            ])
            ->groups([
                Group::make('entity_table')
                    ->label('Entity (Table)')
                    ->collapsible(),
                Group::make('action')
                    ->label('Action Type')
                    ->collapsible(),
                Group::make('actor_user_id')
                    ->label('User')
                    ->getTitleFromRecordUsing(fn($record) => $record->actor?->name ?? 'System/Unknown')
                    ->collapsible(),
            ])
            ->recordUrl(fn() => null)
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
