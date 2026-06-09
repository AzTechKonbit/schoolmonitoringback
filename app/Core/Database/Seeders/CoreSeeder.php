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
        $school = School::updateOrCreate([
            'name' => 'École Primaire Saint-Jean',
            'academic_year' => '2025-2026',
        ],
            [
                'type' => 'primaire',
                'default_language' => 'fr',
                'status' => 'active',
            ]);
        $admin = User::updateOrCreate([
            'email' => 'admin@school.com',

        ],
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'password' => Hash::make('password123'),
                'phone' => '+2250700000000',
                'role' => 'employee',
                'status' => 'active',
            ]);


//        School::factory(50);
//        User::factory(50);

    }
}
