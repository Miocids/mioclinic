<?php

namespace App\Filament\Resources\MedicalRecords;

use App\Filament\Resources\MedicalRecords\Pages\CreateMedicalRecord;
use App\Filament\Resources\MedicalRecords\Pages\EditMedicalRecord;
use App\Filament\Resources\MedicalRecords\Pages\ListMedicalRecords;
use App\Filament\Resources\MedicalRecords\RelationManagers\ClinicalStudiesRelationManager;
use App\Filament\Resources\MedicalRecords\RelationManagers\ConsultationsRelationManager;
use App\Filament\Resources\MedicalRecords\RelationManagers\TreatmentsRelationManager;
use App\Filament\Resources\MedicalRecords\Schemas\MedicalRecordForm;
use App\Filament\Resources\MedicalRecords\Tables\MedicalRecordsTable;
use App\Models\MedicalRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'Historia Clínica';

    protected static ?string $pluralModelLabel = 'Historias Clínicas';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Pacientes';
    }

    public static function form(Schema $schema): Schema
    {
        return MedicalRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ConsultationsRelationManager::class,
            TreatmentsRelationManager::class,
            ClinicalStudiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicalRecords::route('/'),
            'create' => CreateMedicalRecord::route('/create'),
            'edit' => EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}
