<?php

namespace App\Filament\Resources\MedicalRecords\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TreatmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'treatments';

    protected static ?string $title = 'Tratamientos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Tratamiento / Medicamento')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('doctor_id')
                    ->label('Prescrito por')
                    ->relationship('doctor.user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('dosage')
                    ->label('Dosis / Posología'),
                Textarea::make('description')
                    ->label('Descripción / Instrucciones')
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->label('Fecha inicio')
                    ->native(false)
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Fecha fin')
                    ->native(false),
                Toggle::make('is_active')
                    ->label('Tratamiento activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Tratamiento')
                    ->searchable(),
                TextColumn::make('doctor.user.name')
                    ->label('Médico'),
                TextColumn::make('dosage')
                    ->label('Dosis')
                    ->limit(40),
                TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date('d/m/Y'),
                TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y'),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Activo'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
