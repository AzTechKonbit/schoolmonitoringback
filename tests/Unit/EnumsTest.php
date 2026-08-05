<?php

namespace Tests\Unit;

use App\Enums\AttendanceStatus;
use App\Enums\ContractType;
use App\Enums\EmploymentStatus;
use App\Enums\PaymentMethod;
use App\Enums\ProgramType;
use App\Enums\SalaryType;
use App\Enums\SchoolType;
use App\Enums\SexeRole;
use App\Enums\Status;
use App\Enums\TargetRole;
use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    public function test_attendance_status_values(): void
    {
        $this->assertSame('present', AttendanceStatus::PRESENT->value);
        $this->assertSame('absent', AttendanceStatus::ABSENT->value);
        $this->assertSame('late', AttendanceStatus::LATE->value);
        $this->assertSame('excused', AttendanceStatus::EXCUSED->value);
        $this->assertCount(4, AttendanceStatus::cases());
    }

    public function test_contract_type_values(): void
    {
        $this->assertSame('full-time', ContractType::FULL_TIME->value);
        $this->assertSame('part-time', ContractType::PART_TIME->value);
        $this->assertSame('contractor', ContractType::CONTRACTOR->value);
    }

    public function test_employment_status_values(): void
    {
        $this->assertSame('active', EmploymentStatus::ACTIVE->value);
        $this->assertSame('on_leave', EmploymentStatus::ON_LEAVE->value);
        $this->assertSame('terminated', EmploymentStatus::TERMINATED->value);
    }

    public function test_payment_method_values(): void
    {
        $this->assertSame('cash', PaymentMethod::CASH->value);
        $this->assertSame('card', PaymentMethod::CARD->value);
        $this->assertSame('bank_transfer', PaymentMethod::BANK_TRANSFER->value);
        $this->assertSame('mobile_money', PaymentMethod::MOBILE_MONEY->value);
    }

    public function test_program_type_values(): void
    {
        $this->assertSame('master', ProgramType::MASTER->value);
        $this->assertSame('bachelor', ProgramType::BACHELOR->value);
        $this->assertSame('certification', ProgramType::CERTIFICATION->value);
        $this->assertSame('doctorat', ProgramType::DOCTORAT->value);
    }

    public function test_salary_type_values(): void
    {
        $this->assertSame('hourly', SalaryType::HOURLY->value);
        $this->assertSame('monthly', SalaryType::MONTHLY->value);
    }

    public function test_school_type_values(): void
    {
        $this->assertSame('primaire', SchoolType::PRIMAIRE->value);
        $this->assertSame('secondaire', SchoolType::SECONDAIRE->value);
        $this->assertSame('superieur', SchoolType::SUPERIEUR->value);
    }

    public function test_sexe_role_values(): void
    {
        $this->assertSame('F', SexeRole::FEMALE->value);
        $this->assertSame('M', SexeRole::MALE->value);
        $this->assertSame('O', SexeRole::OTHER->value);
    }

    public function test_status_values(): void
    {
        $this->assertSame('active', Status::ACTIVE->value);
        $this->assertSame('inactive', Status::INACTIVE->value);
        $this->assertSame('pending', Status::PENDING->value);
    }

    public function test_target_role_values(): void
    {
        $this->assertSame('all', TargetRole::ALL->value);
        $this->assertSame('teacher', TargetRole::TEACHER->value);
        $this->assertSame('student', TargetRole::STUDENT->value);
        $this->assertSame('parent', TargetRole::PARENT->value);
    }

    public function test_user_role_values(): void
    {
        $this->assertSame('parent', UserRole::PARENT->value);
        $this->assertSame('student', UserRole::STUDENT->value);
        $this->assertSame('employee', UserRole::EMPLOYEE->value);
        $this->assertSame('administrator', UserRole::ADMIN->value);
    }

    public function test_user_role_all_cases(): void
    {
        $this->assertCount(4, UserRole::cases());
        $this->assertContains(UserRole::ADMIN, UserRole::cases());
        $this->assertIsObject(UserRole::tryFrom('student'));
        $this->assertNull(UserRole::tryFrom('unknown'));
    }
}