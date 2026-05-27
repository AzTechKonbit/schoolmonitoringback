<?php

namespace Modules\Academic\Models;

use App\Model;
use App\Core\UserManagement\Models\{Student,Employee};
use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'schedule_id',
        'recorded_by',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'status' => AttendanceStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }
            protected static function newFactory()
    {
        return \Modules\Academic\Database\Factories\AttendanceFactory::new();
    }
}
