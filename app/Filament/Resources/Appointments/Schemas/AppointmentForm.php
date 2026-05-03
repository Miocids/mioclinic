<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('patient_id')
                    ->label('Paciente')
                    ->relationship(
                        name: 'patient',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $team = Filament::getTenant();

                            return $query
                                ->join('users', 'users.id', '=', 'patients.user_id')
                                ->where('patients.team_id', $team->id)
                                ->select('patients.*', 'users.name', 'users.first_name', 'users.last_name');
                        },
                    )
                    ->getOptionLabelFromRecordUsing(fn (Patient $record) => ($record->first_name && $record->last_name)
                        ? "{$record->first_name} {$record->last_name}"
                        : $record->name
                    )
                    ->searchable(['name', 'first_name', 'last_name'])
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre de usuario')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('first_name')
                            ->label('Nombre')
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Apellido')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique('users', 'email')
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $team = Filament::getTenant();

                        $user = User::create([
                            'name' => $data['name'],
                            'first_name' => $data['first_name'] ?? null,
                            'last_name' => $data['last_name'] ?? null,
                            'email' => $data['email'],
                            'password' => bcrypt($data['password']),
                        ]);

                        $user->teams()->attach($team->id);

                        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
                        $user->assignRole('paciente');
                        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

                        return $user->patients()->where('team_id', $team->id)->value('id');
                    }),
                Select::make('doctor_id')
                    ->label('Médico')
                    ->relationship(
                        name: 'doctor',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $team = Filament::getTenant();

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
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('specialty_id', null))
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre de usuario')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('first_name')
                            ->label('Nombre')
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Apellido')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique('users', 'email')
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255),
                        Select::make('specialty_ids')
                            ->label('Especialidades')
                            ->options(Specialty::query()->pluck('name', 'id'))
                            ->multiple()
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $team = Filament::getTenant();

                        $user = User::create([
                            'name' => $data['name'],
                            'first_name' => $data['first_name'] ?? null,
                            'last_name' => $data['last_name'] ?? null,
                            'email' => $data['email'],
                            'password' => bcrypt($data['password']),
                        ]);

                        $user->teams()->attach($team->id);

                        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
                        $user->assignRole('medico');
                        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

                        $doctorId = $user->doctors()->where('team_id', $team->id)->value('id');

                        Doctor::find($doctorId)?->specialties()->attach($data['specialty_ids'] ?? []);

                        return $doctorId;
                    }),
                Select::make('specialty_id')
                    ->label('Especialidad')
                    ->options(fn (Get $get) => Doctor::find($get('doctor_id'))
                        ?->specialties()->pluck('name', 'specialties.id')
                        ?? Specialty::query()->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Fecha y hora')
                    ->native(false)
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('fee')
                    ->label('Honorario')
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}
