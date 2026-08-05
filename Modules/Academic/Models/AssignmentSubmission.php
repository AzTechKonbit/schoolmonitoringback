<?php

namespace Modules\Academic\Models;

use App\Core\UserManagement\Models\Student;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $table = 'assignment_submissions';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'grade',
        'submitted_at',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected static function newFactory()
    {
        return \Modules\Academic\Database\Factories\AssignmentSubmissionFactory::new();
    }
}