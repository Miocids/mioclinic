<?php

namespace App\Filament\Resources\Treatments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TreatmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('medical_record_id')
                    ->label('Historia Clínica (Paciente)')
                    ->relationship('medicalRecord.patient.user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Select::make('doctor_id')
                    ->label('Prescrito por')
                    ->relationship('doctor.user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Tratamiento / Medicamento')
                    ->required()
                    ->maxLength(255),
                Textarea::make('dosage')
                    ->label('Dosis / Posología'),
                Textarea::make('description')
                    ->label('Descripción'),
                DatePicker::make('start_date')
                    ->label('Fecha inicio')
                    ->native(false)
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Fecha fin')
                    ->native(false),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
