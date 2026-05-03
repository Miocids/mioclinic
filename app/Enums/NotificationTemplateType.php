<?php

namespace App\Enums;

enum NotificationTemplateType: string
{
    case AppointmentConfirmedPatient = 'appointment_confirmed_patient';
    case AppointmentConfirmedDoctor = 'appointment_confirmed_doctor';
    case AppointmentCancelledPatient = 'appointment_cancelled_patient';
    case AppointmentCancelledDoctor = 'appointment_cancelled_doctor';
    case AppointmentReminder = 'appointment_reminder';

    public function label(): string
    {
        return match ($this) {
            self::AppointmentConfirmedPatient => 'Cita confirmada — Paciente',
            self::AppointmentConfirmedDoctor => 'Cita confirmada — Médico',
            self::AppointmentCancelledPatient => 'Cita cancelada — Paciente',
            self::AppointmentCancelledDoctor => 'Cita cancelada — Médico',
            self::AppointmentReminder => 'Recordatorio de cita',
        };
    }

    public function defaultSubject(): string
    {
        return match ($this) {
            self::AppointmentConfirmedPatient => 'Tu cita ha sido confirmada — {{clinica_nombre}}',
            self::AppointmentConfirmedDoctor => 'Nueva cita confirmada con {{paciente_nombre}}',
            self::AppointmentCancelledPatient => 'Tu cita ha sido cancelada — {{clinica_nombre}}',
            self::AppointmentCancelledDoctor => 'Cita cancelada con {{paciente_nombre}}',
            self::AppointmentReminder => 'Recordatorio: tienes una cita mañana — {{clinica_nombre}}',
        };
    }

    public function defaultBody(): string
    {
        return match ($this) {
            self::AppointmentConfirmedPatient => "Hola {{paciente_nombre}},\n\nTu consulta ha sido confirmada con los siguientes detalles:\n\n- Médico: {{medico_nombre}}\n- Especialidad: {{especialidad}}\n- Fecha: {{fecha}}\n- Hora: {{hora}}\n\nCentro Médico: {{clinica_nombre}}\nDirección: {{clinica_direccion}}\nTeléfono: {{clinica_telefono}}\n\nPor favor llega 10 minutos antes de tu cita.\n\nSaludos,\n{{clinica_nombre}}",
            self::AppointmentConfirmedDoctor => "Hola Dr. {{medico_nombre}},\n\nTiene una nueva cita confirmada:\n\n- Paciente: {{paciente_nombre}}\n- Especialidad: {{especialidad}}\n- Fecha: {{fecha}}\n- Hora: {{hora}}\n\nSaludos,\n{{clinica_nombre}}",
            self::AppointmentCancelledPatient => "Hola {{paciente_nombre}},\n\nLamentamos informarte que tu cita ha sido cancelada:\n\n- Médico: {{medico_nombre}}\n- Fecha: {{fecha}}\n- Hora: {{hora}}\n\nPor favor contáctanos para reagendar: {{clinica_telefono}}\n\nSaludos,\n{{clinica_nombre}}",
            self::AppointmentCancelledDoctor => "Hola Dr. {{medico_nombre}},\n\nLa siguiente cita ha sido cancelada:\n\n- Paciente: {{paciente_nombre}}\n- Fecha: {{fecha}}\n- Hora: {{hora}}\n\nSaludos,\n{{clinica_nombre}}",
            self::AppointmentReminder => "Hola {{paciente_nombre}},\n\nTe recordamos que tienes una cita mañana:\n\n- Médico: {{medico_nombre}}\n- Especialidad: {{especialidad}}\n- Fecha: {{fecha}}\n- Hora: {{hora}}\n\nCentro Médico: {{clinica_nombre}}\nDirección: {{clinica_direccion}}\n\nSaludos,\n{{clinica_nombre}}",
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }

    /** @return array<string> */
    public static function variables(): array
    {
        return [
            '{{paciente_nombre}}',
            '{{medico_nombre}}',
            '{{especialidad}}',
            '{{fecha}}',
            '{{hora}}',
            '{{clinica_nombre}}',
            '{{clinica_direccion}}',
            '{{clinica_telefono}}',
            '{{clinica_email}}',
        ];
    }
}
