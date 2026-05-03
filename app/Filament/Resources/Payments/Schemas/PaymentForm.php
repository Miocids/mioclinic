<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('patient_id')
                    ->label('Paciente')
                    ->relationship('patient.user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('appointment_id')
                    ->label('Consulta')
                    ->relationship('appointment', 'scheduled_at')
                    ->searchable(),
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'cancelled' => 'Cancelado',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('description')
                    ->label('Descripción')
                    ->maxLength(255),
                Select::make('payment_method')
                    ->label('Método de pago')
                    ->options([
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        'insurance' => 'Seguro médico',
                    ]),
                DateTimePicker::make('paid_at')
                    ->label('Fecha de pago')
                    ->native(false),
            ]);
    }
}
