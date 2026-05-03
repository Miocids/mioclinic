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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConsultationsRelationManager extends RelationManager
{
    protected static string $relationship = 'consultations';

    protected static ?string $title = 'Consultas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DatePicker::make('consultation_date')
                    ->label('Fecha de consulta')
                    ->native(false)
                    ->required(),
                Select::make('doctor_id')
                    ->label('Médico')
                    ->relationship('doctor.user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('specialty_id')
                    ->label('Especialidad')
                    ->relationship('specialty', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('reason')
                    ->label('Motivo de consulta')
                    ->columnSpanFull(),
                Textarea::make('diagnosis')
                    ->label('Diagnóstico')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notas / Plan de tratamiento')
                    ->columnSpanFull(),
                Textarea::make('pending_references')
                    ->label('Referencias pendientes / Estudios solicitados')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('consultation_date')
            ->defaultSort('consultation_date', 'desc')
            ->columns([
                TextColumn::make('consultation_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('doctor.user.name')
                    ->label('Médico')
                    ->searchable(),
                TextColumn::make('specialty.name')
                    ->label('Especialidad')
                    ->badge(),
                TextColumn::make('diagnosis')
                    ->label('Diagnóstico')
                    ->limit(60),
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
