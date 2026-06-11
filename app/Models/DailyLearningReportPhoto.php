<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLearningReportPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_learning_report_id',
        'domain_code',
        'slot',
        'title',
        'image_path',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyLearningReport::class, 'daily_learning_report_id');
    }
}
