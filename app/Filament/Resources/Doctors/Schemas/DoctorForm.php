<?php

namespace App\Filament\Resources\Doctors\Schemas;

use App\Models\Specialty;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
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
                                        ->where('name', 'medico')
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
                                $user->assignRole('medico');
                                app(PermissionRegistrar::class)->setPermissionsTeamId(null);

                                return $user->getKey();
                            }),
                        TextInput::make('cedula')
                            ->label('Cédula de identidad')
                            ->maxLength(20),
                        TextInput::make('legal_registration')
                            ->label('Registro legal / SENESCYT')
                            ->maxLength(50),
                        TextInput::make('contact')
                            ->label('Contacto / Teléfono')
                            ->maxLength(50),
                    ]),
                Section::make('Perfil Profesional')
                    ->columns(1)
                    ->schema([
                        Select::make('specialties')
                            ->label('Especialidades')
                            ->relationship('specialties', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Textarea::make('training_centers')
                            ->label('Centros de formación')
                            ->rows(3),
                        Textarea::make('curriculum')
                            ->label('Resumen curricular')
                            ->rows(5),
                        Textarea::make('rates')
                            ->label('Tarifas')
                            ->rows(3),
                        Textarea::make('bio')
                            ->label('Biografía / Presentación')
                            ->rows(4),
                    ]),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
