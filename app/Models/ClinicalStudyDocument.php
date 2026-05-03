<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalStudyDocument extends Model
{
    public function clinicalStudy(): BelongsTo
    {
        return $this->belongsTo(ClinicalStudy::class);
    }
}
