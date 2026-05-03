<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos personales')
                ->columns(3)
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Usuario'),
                    TextEntry::make('user.first_name')
                        ->label('Nombre'),
                    TextEntry::make('user.last_name')
                        ->label('Apellido'),
                    TextEntry::make('cedula')
                        ->label('Cédula'),
                    TextEntry::make('phone')
                        ->label('Teléfono'),
                    TextEntry::make('birth_date')
                        ->label('Fecha de nacimiento')
                        ->date('d/m/Y'),
                    TextEntry::make('age')
                        ->label('Edad')
                        ->suffix(' años'),
                    TextEntry::make('blood_type')
                        ->label('Tipo de sangre')
                        ->badge(),
                    TextEntry::make('address')
                        ->label('Dirección'),
                ]),
            Section::make('Contacto de emergencia')
                ->columns(2)
                ->schema([
                    TextEntry::make('emergency_contact_name')
                        ->label('Nombre'),
                    TextEntry::make('emergency_contact_phone')
                        ->label('Teléfono'),
                ]),
            Section::make('Historia Clínica')
                ->schema([
                    TextEntry::make('medicalRecord.doctor.user.name')
                        ->label('Médico tratante'),
                    TextEntry::make('medicalRecord.allergies')
                        ->label('Alergias')
                        ->placeholder('Sin alergias registradas')
                        ->columnSpanFull(),
                    TextEntry::make('medicalRecord.background')
                        ->label('Antecedentes médicos')
                        ->placeholder('Sin antecedentes registrados')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
