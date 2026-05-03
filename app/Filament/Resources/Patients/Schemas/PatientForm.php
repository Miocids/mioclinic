<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos personales')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Usuario')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query) {
                                    $team = Filament::getTenant();

                                    return $query->whereHas('roles', fn (Builder $q) => $q
                                        ->where('name', 'paciente')
                                        ->where('roles.team_id', $team->id)
                                    );
                                },
                            )
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
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
                                    'name'       => $data['name'],
                                    'first_name' => $data['first_name'] ?? null,
                                    'last_name'  => $data['last_name'] ?? null,
                                    'email'      => $data['email'],
                                    'password'   => bcrypt($data['password']),
                                ]);

                                $user->teams()->attach($team->id);

                                app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
                                $user->assignRole('paciente');
                                app(PermissionRegistrar::class)->setPermissionsTeamId(null);

                                return $user->getKey();
                            }),
                        TextInput::make('cedula')
                            ->label('Cédula de identidad')
                            ->maxLength(20),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                        DatePicker::make('birth_date')
                            ->label('Fecha de nacimiento')
                            ->native(false),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('blood_type')
                            ->label('Tipo de sangre')
                            ->options([
                                'A+' => 'A+', 'A-' => 'A-',
                                'B+' => 'B+', 'B-' => 'B-',
                                'AB+' => 'AB+', 'AB-' => 'AB-',
                                'O+' => 'O+', 'O-' => 'O-',
                            ]),
                    ]),
                Section::make('Contacto de emergencia')
                    ->columns(2)
                    ->schema([
                        TextInput::make('emergency_contact_name')
                            ->label('Nombre')
                            ->maxLength(100),
                        TextInput::make('emergency_contact_phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                    ]),
            ]);
    }
}
