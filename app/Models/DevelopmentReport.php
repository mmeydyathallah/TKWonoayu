<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentReport extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'semester',
        'school_year',
        'summary',
        'teacher_note',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

