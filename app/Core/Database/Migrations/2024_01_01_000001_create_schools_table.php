<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('type', 45)->nullable();
            $table->string('logo', 45)->nullable();
            $table->string('default_language', 45)->nullable();
            $table->string('academic_year', 45)->nullable();
            $table->string('status', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
