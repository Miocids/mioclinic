<?php

namespace App\Filament\Resources\MedicalRecords\Schemas;

use App\Models\Doctor;
use App\Models\Patient;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class MedicalRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = auth()->user();
        $team = Filament::getTenant();
        $isMedico = $user?->hasRole('medico');
        $doctor = $isMedico
            ? Doctor::where('user_id', $user->id)->where('team_id', $team->id)->first()
            : null;
        $fromParams = request()->hasAny(['patient_id', 'doctor_id']);

        return $schema
            ->components([
                Section::make('Médico y Paciente')
                    ->schema([
                        Select::make('doctor_id')
                            ->label('Médico')
                            ->relationship(
                                name: 'doctor',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query) use ($team) {
                                    return $query
                                        ->join('users', 'users.id', '=', 'doctors.user_id')
                                        ->where('doctors.team_id', $team->id)
                                        ->select('doctors.*', 'users.name', 'users.first_name', 'users.last_name');
                                },
                            )
                            ->getOptionLabelFromRecordUsing(fn (Doctor $record) => ($record->first_name && $record->last_name)
                                ? "{$record->first_name} {$record->last_name}"
                                : $record->name
                            )
                            ->searchable(['name', 'first_name', 'last_name'])
                            ->preload()
                            ->default($doctor?->id)
                            ->hidden($isMedico)
                            ->disabled($fromParams)
                            ->dehydrated()
                            ->live()
                            ->required(),
                        Select::make('patient_id')
                            ->label('Paciente')
                            ->options(function (Get $get) use ($doctor, $isMedico, $team) {
                                $doctorId = $isMedico ? $doctor?->id : $get('doctor_id');

                                if ($doctorId) {
                                    return Doctor::find($doctorId)
                                        ?->patients()
                                        ->join('users', 'users.id', '=', 'patients.user_id')
                                        ->selectRaw("patients.id, CASE WHEN users.first_name IS NOT NULL AND users.last_name IS NOT NULL THEN CONCAT(users.first_name, ' ', users.last_name) ELSE users.name END as display_name")
                                        ->pluck('display_name', 'patients.id')
                                        ?? collect();
                                }

                                return Patient::join('users', 'users.id', '=', 'patients.user_id')
                                    ->where('patients.team_id', $team->id)
                                    ->selectRaw("patients.id, CASE WHEN users.first_name IS NOT NULL AND users.last_name IS NOT NULL THEN CONCAT(users.first_name, ' ', users.last_name) ELSE users.name END as display_name")
                                    ->pluck('display_name', 'patients.id');
                            })
                            ->searchable()
                            ->disabled($fromParams)
                            ->dehydrated()
                            ->required(),
                    ]),
                Section::make('Antecedentes')
                    ->schema([
                        Textarea::make('allergies')
                            ->label('Alergias')
                            ->rows(3),
                        Textarea::make('background')
                            ->label('Antecedentes médicos')
                            ->rows(5),
                    ]),
            ]);
    }
}
