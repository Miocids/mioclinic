<?php

namespace App\Filament\Resources\MedicalRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.user.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patient.birth_date')
                    ->label('Fecha nacimiento')
                    ->date('d/m/Y'),
                TextColumn::make('patient.blood_type')
                    ->label('Tipo sangre')
                    ->badge(),
                TextColumn::make('consultations_count')
                    ->label('Consultas')
                    ->counts('consultations'),
                TextColumn::make('active_treatments_count')
                    ->label('Tratamientos activos')
                    ->counts('activeTreatments'),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
