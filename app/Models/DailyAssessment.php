<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAssessment extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'assessed_on',
        'class_group',
        'activity',
        'aspect_code',
        'aspect_name',
        'score_label',
        'score_value',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'assessed_on' => 'date',
            'score_value' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

