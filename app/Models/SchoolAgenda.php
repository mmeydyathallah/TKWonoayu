<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolAgenda extends Model
{
    protected $table = 'school_agendas';

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'end_date',
        'type',
        'is_public',
        'color',
        'created_by'
    ];

    protected $casts = [
        'event_date' => 'date',
        'end_date' => 'date',
        'is_public' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
