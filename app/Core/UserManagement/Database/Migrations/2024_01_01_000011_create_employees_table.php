<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 45)->unique()->comment('unique, human-readable like EMP-000123');
            $table->string('employment_status', 45)->default('active')->comment('enum: active/on_leave/terminated');
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->string('national_id', 45)->nullable();
            $table->string('emergency_contact_name', 45)->nullable();
            $table->string('emergency_contact_phone', 45)->nullable();
            $table->string('employment_contract_type', 45)->nullable()->comment('full-time/part-time/contractor');
            $table->string('salary_type', 45)->default('monthly')->comment('hourly/monthly');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->foreignId('employee_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
