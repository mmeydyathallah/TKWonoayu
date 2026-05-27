<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'student_no',
        'rfid_code',
        'nisn',
        'class_group',
        'school_year',
        'full_name',
        'nickname',
        'nik',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'sibling_order',
        'siblings_total',
        'address',
        'phone_number',
        'distance_to_school_km',
        'weight_kg',
        'height_cm',
        'head_circumference_cm',
        'blood_type',
        'health_history',
        'avatar_url',
        'user_id',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function dailyAssessments(): HasMany
    {
        return $this->hasMany(DailyAssessment::class);
    }

    public function checklistAssessments(): HasMany
    {
        return $this->hasMany(ChecklistAssessment::class);
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    public function anecdotalNotes(): HasMany
    {
        return $this->hasMany(AnecdotalNote::class);
    }

    public function developmentReports(): HasMany
    {
        return $this->hasMany(DevelopmentReport::class);
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }
}
