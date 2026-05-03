<?php

namespace App\Filament\Resources\Doctors\RelationManagers;

use App\Filament\Resources\MedicalRecords\MedicalRecordResource;
use App\Filament\Resources\Patients\PatientResource;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientsRelationManager extends RelationManager
{
    protected static string $relationship = 'patients';

    protected static ?string $title = 'Pacientes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('user.first_name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('user.last_name')
                    ->label('Apellido')
                    ->searchable(),
                TextColumn::make('cedula')
                    ->label('Cédula')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Teléfono'),
                TextColumn::make('age')
                    ->label('Edad')
                    ->suffix(' años'),
                TextColumn::make('blood_type')
                    ->label('Sangre')
                    ->badge(),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('nueva_historia')
                        ->label('Nueva Historia Clínica')
                        ->icon('heroicon-o-plus-circle')
                        ->url(function (Patient $record) {
                            $doctor = $this->ownerRecord;

                            $existing = MedicalRecord::where('patient_id', $record->id)
                                ->where('team_id', $doctor->team_id)
                                ->first();

                            if ($existing) {
                                return MedicalRecordResource::getUrl('edit', ['record' => $existing]);
                            }

                            return MedicalRecordResource::getUrl('create').'?'.http_build_query([
                                'patient_id' => $record->id,
                                'doctor_id' => $doctor->id,
                            ]);
                        }),
                    Action::make('ver_historia')
                        ->label('Ver Historia Clínica')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (Patient $record) => PatientResource::getUrl('view', ['record' => $record])),
                    DetachAction::make()
                        ->label('Desvincular'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
