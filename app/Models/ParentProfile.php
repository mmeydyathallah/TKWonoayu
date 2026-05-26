<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentProfile extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'guardian_name',
        'guardian_email',
        'guardian_phone',
        'father_name',
        'father_nik',
        'father_job',
        'father_phone',
        'mother_name',
        'mother_nik',
        'mother_job',
        'mother_phone',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

