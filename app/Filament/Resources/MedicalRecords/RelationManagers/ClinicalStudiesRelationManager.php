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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClinicalStudiesRelationManager extends RelationManager
{
    protected static string $relationship = 'clinicalStudies';

    protected static ?string $title = 'Estudios Clínicos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nombre del estudio')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('ordered_by')
                    ->label('Solicitado por')
                    ->relationship('orderedBy.user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('ordered_at')
                    ->label('Fecha de solicitud')
                    ->native(false)
                    ->required(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                DatePicker::make('results_date')
                    ->label('Fecha de resultados')
                    ->native(false),
                Textarea::make('results_notes')
                    ->label('Notas de resultados')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('ordered_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Estudio')
                    ->searchable(),
                TextColumn::make('orderedBy.user.name')
                    ->label('Solicitado por'),
                TextColumn::make('ordered_at')
                    ->label('Solicitado')
                    ->date('d/m/Y'),
                TextColumn::make('results_date')
                    ->label('Resultados')
                    ->date('d/m/Y'),
                TextColumn::make('results_notes')
                    ->label('Notas')
                    ->limit(50),
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
