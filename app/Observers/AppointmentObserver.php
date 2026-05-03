<?php

namespace App\Observers;

use App\Enums\NotificationTemplateType;
use App\Jobs\SendAppointmentNotificationJob;
use App\Models\Appointment;

class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        $appointment->doctor->patients()->syncWithoutDetaching([
            $appointment->patient_id => ['team_id' => $appointment->team_id],
        ]);
    }

    public function updated(Appointment $appointment): void
    {
        if (! $appointment->wasChanged('status')) {
            return;
        }

        match ($appointment->status) {
            'confirmed' => $this->dispatchConfirmation($appointment),
            'cancelled' => $this->dispatchCancellation($appointment),
            default => null,
        };
    }

    private function dispatchConfirmation(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user']);

        $patientEmail = $appointment->patient->user->email;
        $patientName = $appointment->patient->user->name;

        $doctorEmail = $appointment->doctor->user->email;
        $doctorName = $appointment->doctor->user->name;

        SendAppointmentNotificationJob::dispatch(
            $appointment->id,
            NotificationTemplateType::AppointmentConfirmedPatient,
            $patientEmail,
            $patientName,
        );

        SendAppointmentNotificationJob::dispatch(
            $appointment->id,
            NotificationTemplateType::AppointmentConfirmedDoctor,
            $doctorEmail,
            $doctorName,
        );
    }

    private function dispatchCancellation(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user']);

        SendAppointmentNotificationJob::dispatch(
            $appointment->id,
            NotificationTemplateType::AppointmentCancelledPatient,
            $appointment->patient->user->email,
            $appointment->patient->user->name,
        );

        SendAppointmentNotificationJob::dispatch(
            $appointment->id,
            NotificationTemplateType::AppointmentCancelledDoctor,
            $appointment->doctor->user->email,
            $appointment->doctor->user->name,
        );
    }
}
