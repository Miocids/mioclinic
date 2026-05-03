<?php

namespace App\Filament\Resources\NotificationTemplates\Tables;

use App\Enums\NotificationTemplateType;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NotificationTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Evento')
                    ->formatStateUsing(fn (NotificationTemplateType $state) => $state->label())
                    ->badge(),
                TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(60),
                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Modificada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Evento')
                    ->options(NotificationTemplateType::options()),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
