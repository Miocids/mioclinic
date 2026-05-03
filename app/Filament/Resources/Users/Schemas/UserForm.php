<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información personal')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Nombre')
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Apellido')
                            ->maxLength(255),
                        TextInput::make('name')
                            ->label('Nombre de usuario')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(table: 'users', column: 'email', ignoreRecord: true)
                            ->maxLength(255),
                    ]),
                Section::make('Acceso')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),
                        Select::make('role')
                            ->label('Rol')
                            ->options(fn () => Role::where('team_id', Filament::getTenant()->id)
                                ->pluck('name', 'name')
                                ->map(fn ($name) => ucfirst(str_replace('_', ' ', $name)))
                            )
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }
}
