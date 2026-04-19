<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = app()->tagged('module.seeders');
        foreach ($seeders as $seeder) {
            $seeder->run();
        }
    }
}
