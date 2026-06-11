<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyLearningReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessed_on',
        'class_group',
        'agama_budi_pekerti_score',
        'agama_budi_pekerti_narrative',
        'jati_diri_score',
        'jati_diri_narrative',
        'literasi_steam_score',
        'literasi_steam_narrative',
        'kokurikuler_description',
        'extracurricular_implementation',
        'extracurricular_activity',
        'extracurricular_score_label',
    ];

    protected function casts(): array
    {
        return [
            'assessed_on' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DailyLearningReportPhoto::class);
    }

    public function extracurricularItems(): HasMany
    {
        return $this->hasMany(DailyLearningReportExtracurricular::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
