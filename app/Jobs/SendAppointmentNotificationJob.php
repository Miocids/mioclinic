<?php

namespace App\Jobs;

use App\Enums\NotificationTemplateType;
use App\Models\Appointment;
use App\Models\NotificationTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendAppointmentNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly int $appointmentId,
        public readonly NotificationTemplateType $templateType,
        public readonly string $recipientEmail,
        public readonly string $recipientName,
    ) {}

    public function handle(): void
    {
        $appointment = Appointment::with([
            'patient.user',
            'doctor.user',
            'specialty',
            'team',
        ])->find($this->appointmentId);

        if (! $appointment) {
            return;
        }

        $template = NotificationTemplate::findForTeam(
            $appointment->team_id,
            $this->templateType,
        );

        if (! $template) {
            return;
        }

        $variables = $this->buildVariables($appointment);
        ['subject' => $subject, 'body' => $body] = $template->render($variables);

        Mail::raw($body, function ($message) use ($subject) {
            $message
                ->to($this->recipientEmail, $this->recipientName)
                ->subject($subject);
        });
    }

    /** @return array<string, string> */
    private function buildVariables(Appointment $appointment): array
    {
        return [
            '{{paciente_nombre}}' => $appointment->patient->user->name,
            '{{medico_nombre}}' => $appointment->doctor->user->name,
            '{{especialidad}}' => $appointment->specialty->name,
            '{{fecha}}' => $appointment->scheduled_at->format('d/m/Y'),
            '{{hora}}' => $appointment->scheduled_at->format('H:i'),
            '{{clinica_nombre}}' => $appointment->team->name,
            '{{clinica_direccion}}' => $appointment->team->address ?? '',
            '{{clinica_telefono}}' => $appointment->team->phone ?? '',
            '{{clinica_email}}' => $appointment->team->email ?? '',
        ];
    }
}
