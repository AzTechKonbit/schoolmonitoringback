<?php

namespace Modules\Academic\Models;


use App\Model;

class Term extends Model
{
    protected $fillable = [
        'term',
        'start_date',
        'end_date',
        'school_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

//    public function assignments(): HasMany
//    {
//        return $this->hasMany(Assignment::class);
//    }

    public function isCurrent(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    protected static function newFactory()
    {
        return \Modules\Academic\Database\Factories\TermFactory::new();
    }
}
