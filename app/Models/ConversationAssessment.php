<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationAssessment extends Model
{
    protected $fillable = [
        'student_id',
        'assessed_on',
        'activity',
        'aspect',
        'score_label',
    ];

    protected function casts(): array
    {
        return [
            'assessed_on' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }}
