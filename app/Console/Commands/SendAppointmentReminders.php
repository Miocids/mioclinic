<?php

namespace App\Console\Commands;

use App\Enums\NotificationTemplateType;
use App\Jobs\SendAppointmentNotificationJob;
use App\Models\Appointment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mioclinic:send-appointment-reminders {--hours=24 : Hours before appointment to send reminder}')]
#[Description('Send appointment reminder emails to patients')]
class SendAppointmentReminders extends Command
{
    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        $windowStart = now()->addHours($hours)->startOfHour();
        $windowEnd = $windowStart->copy()->endOfHour();

        $appointments = Appointment::with(['patient.user', 'doctor.user', 'specialty', 'team'])
            ->where('status', 'confirmed')
            ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
            ->get();

        if ($appointments->isEmpty()) {
            $this->info("No appointments found in window {$windowStart} – {$windowEnd}.");

            return self::SUCCESS;
        }

        foreach ($appointments as $appointment) {
            SendAppointmentNotificationJob::dispatch(
                $appointment->id,
                NotificationTemplateType::AppointmentReminder,
                $appointment->patient->user->email,
                $appointment->patient->user->name,
            );

            $this->line("Queued reminder → {$appointment->patient->user->name} ({$appointment->scheduled_at->format('d/m/Y H:i')})");
        }

        $this->info("Dispatched {$appointments->count()} reminder(s).");

        return self::SUCCESS;
    }
}
