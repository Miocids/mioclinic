<?php

namespace App\Models;

use App\Enums\NotificationTemplateType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplate extends Model
{
    protected function casts(): array
    {
        return [
            'type' => NotificationTemplateType::class,
            'is_active' => 'boolean',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function render(array $variables): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $subject = str_replace($key, $value, $subject);
            $body = str_replace($key, $value, $body);
        }

        return ['subject' => $subject, 'body' => $body];
    }

    public static function findForTeam(int $teamId, NotificationTemplateType $type): ?self
    {
        return static::where('team_id', $teamId)
            ->where('type', $type->value)
            ->where('is_active', true)
            ->first();
    }
}
