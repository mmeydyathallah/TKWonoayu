<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLearningReportExtracurricular extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_learning_report_id',
        'sort_order',
        'implementation',
        'activity',
        'score_label',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyLearningReport::class, 'daily_learning_report_id');
    }
}
