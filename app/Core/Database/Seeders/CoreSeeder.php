<?php

namespace App\Core\Database\Seeders;

use App\Core\Models\School;
use App\Core\Models\User;
use Database\Seeders\ModuleSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreSeeder extends Seeder implements ModuleSeeder
{
    public function run(): void
    {
        $school = School::create([
            'name' => 'École Primaire Saint-Jean',
            'type' => 'primaire',
            'default_language' => 'fr',
            'academic_year' => '2025-2026',
            'status' => 'active',
        ]);

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@school.com',
            'password' => Hash::make('password123'),
            'phone' => '+2250700000000',
            'role' => 'employee',
            'status' => 'active',
            'school_id' => $school->id,
        ]);
        $admin->createToken('seed-token');
        //$this->command->info('School created: ' . $school->id);
        //$this->command->info('Admin user: admin@school.com / password123');


//        School::factory(50);
//        User::factory(50);

    }
}
