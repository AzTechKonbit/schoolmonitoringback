<?php

namespace App\Core\Authorization\Database\Seeders;

use Database\Seeders\ModuleSeeder;
use Illuminate\Database\Seeder;
use App\Core\Authorization\Models\{Right, Title};

class AuthorizationSeeder extends Seeder implements ModuleSeeder
{
    public function run(): void
    {
        Right::create(['code' => 'SUPER_ADMIN', 'description' => 'Super Administrateur', 'status' => 'active']);
        Right::create(['code' => 'VIEW_FINANCE', 'description' => 'Voir Finance', 'status' => 'active']);
        Right::create(['code' => 'EDIT_FINANCE', 'description' => 'Modifier Finance', 'status' => 'active']);
        Right::create(['code' => 'VIEW_GRADEBOOK', 'description' => 'Voir Cahier de Notes', 'status' => 'active']);
        Right::create(['code' => 'EDIT_GRADEBOOK', 'description' => 'Modifier Cahier de Notes', 'status' => 'active']);
        Right::create(['code' => 'TAKE_ATTENDANCE', 'description' => 'Prendre Présence', 'status' => 'active']);
        Right::create(['code' => 'VIEW_ATTENDANCE', 'description' => 'Voir Présence', 'status' => 'active']);
        Right::create(['code' => 'MANAGE_STUDENTS', 'description' => 'Gérer Étudiants', 'status' => 'active']);
        Right::create(['code' => 'MANAGE_TEACHERS', 'description' => 'Gérer Enseignants', 'status' => 'active']);

        Title::create(['code' => 'DIR', 'title' => 'Directeur', 'description' => 'Directeur']);
        Title::create(['code' => 'DEP', 'title' => 'Chef département', 'description' => 'Chef']);
        Title::create(['code' => 'CP', 'title' => 'Professeur principal', 'description' => 'Prof principal']);

        //$this->command->info('Rights and Titles created');
    }
}
