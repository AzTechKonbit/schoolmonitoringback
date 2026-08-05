<?php

namespace App\Core\UserManagement\Models;

use App\Core\Authorization\Models\Title;
use Modules\Academic\Models\Course;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Core\Models\{School, User};
use App\Enums\ContractType;
use App\Enums\EmploymentStatus;
use App\Enums\SalaryType;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'employment_status',
        'hire_date',
        'termination_date',
        'created_by',
        'national_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'employment_contract_type',
        'salary_type',
        'base_salary',
        'employee_type_id',
        'user_id',
        'school_id',
    ];

    protected $casts = [
        'employment_status' => EmploymentStatus::class,
        'contract_type' => ContractType::class,
        'salary_type' => SalaryType::class,
        'hire_date' => 'date',
        'termination_date' => 'date',
        'base_salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employeeType(): BelongsTo
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'employee_titles', 'employee_id', 'title_id');
    }

    public function isTeacher(): bool
    {
        return $this->teacher()->exists();
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'employees_courses', 'employee_id', 'course_id');
    }

    protected static function newFactory()
    {
        return \App\Core\UserManagement\Database\Factories\EmployeeFactory::new();
    }
}
