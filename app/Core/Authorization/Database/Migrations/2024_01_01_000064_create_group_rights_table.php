<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_rights', function (Blueprint $table) {
            $table->foreignId('right_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['right_id', 'employee_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_rights');
    }
};
