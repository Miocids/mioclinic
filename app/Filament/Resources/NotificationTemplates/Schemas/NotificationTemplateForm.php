<?php

namespace App\Filament\Resources\NotificationTemplates\Schemas;

use App\Enums\NotificationTemplateType;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class NotificationTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        $variables = implode(', ', NotificationTemplateType::variables());

        return $schema
            ->components([
                Section::make('Tipo de notificación')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Evento')
                            ->options(NotificationTemplateType::options())
                            ->required()
                            ->disabledOn('edit'),
                        Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true)
                            ->inline(false),
                    ]),
                Section::make('Contenido del correo')
                    ->description(new HtmlString(
                        '<strong>Variables disponibles:</strong> ' . $variables
                    ))
                    ->schema([
                        TextInput::make('subject')
                            ->label('Asunto')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('body')
                            ->label('Cuerpo del mensaje')
                            ->required()
                            ->rows(12)
                            ->helperText('Usa las variables de arriba para personalizar el mensaje. Ejemplo: "Hola {{paciente_nombre}}, tu cita es el {{fecha}}"'),
                    ]),
            ]);
    }
}
