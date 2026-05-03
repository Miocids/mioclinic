<?php

namespace App\Filament\Resources\Doctors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Horarios de atención';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('day_of_week')
                    ->label('Día')
                    ->options([
                        0 => 'Domingo',
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miércoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sábado',
                    ])
                    ->required(),
                TimePicker::make('start_time')
                    ->label('Hora inicio')
                    ->required(),
                TimePicker::make('end_time')
                    ->label('Hora fin')
                    ->required(),
                Select::make('slot_duration_minutes')
                    ->label('Duración de cita (min)')
                    ->options([15 => '15 min', 20 => '20 min', 30 => '30 min', 45 => '45 min', 60 => '1 hora'])
                    ->default(30)
                    ->required(),
                Toggle::make('is_available')
                    ->label('Disponible')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('day_of_week')
            ->columns([
                TextColumn::make('day_name')
                    ->label('Día')
                    ->sortable(false),
                TextColumn::make('start_time')
                    ->label('Inicio'),
                TextColumn::make('end_time')
                    ->label('Fin'),
                TextColumn::make('slot_duration_minutes')
                    ->label('Duración (min)'),
                IconColumn::make('is_available')
                    ->label('Disponible')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
