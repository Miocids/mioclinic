<?php

namespace App\Filament\Resources\MedicalRecords\Pages;

use App\Filament\Resources\MedicalRecords\MedicalRecordResource;
use App\Models\Doctor;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;

    public function mount(): void
    {
        parent::mount();

        $fill = array_filter([
            'patient_id' => request()->integer('patient_id') ?: null,
            'doctor_id' => request()->integer('doctor_id') ?: null,
        ]);

        if ($fill) {
            $this->form->fill($fill);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        if ($user->hasRole('medico') && empty($data['doctor_id'])) {
            $data['doctor_id'] = Doctor::where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->value('id');
        }

        return $data;
    }
}
