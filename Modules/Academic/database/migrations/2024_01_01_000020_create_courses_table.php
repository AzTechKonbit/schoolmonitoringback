<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->text('description')->nullable();
            $table->string('code', 45)->nullable();
            $table->decimal('credit', 5, 2)->nullable();
            $table->integer('total_hours')->nullable()->comment('total general hours');
            $table->integer('hours_td')->nullable()->comment('travaux diriges hours');
            $table->integer('hours_tp')->nullable()->comment('travaux pratiques hours');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
