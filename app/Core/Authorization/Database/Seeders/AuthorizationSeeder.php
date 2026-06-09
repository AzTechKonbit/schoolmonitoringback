<?php

namespace App\Core\Authorization\Database\Seeders;

use Database\Seeders\ModuleSeeder;
use Illuminate\Database\Seeder;
use App\Core\Authorization\Models\{Right, Title};

class AuthorizationSeeder extends Seeder implements ModuleSeeder
{
    public function run(): void
    {
        Right::updateOrCreate(['code' => 'SUPER_ADMIN', 'description' => 'Super Administrateur', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'VIEW_FINANCE', 'description' => 'Voir Finance', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'EDIT_FINANCE', 'description' => 'Modifier Finance', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'VIEW_GRADEBOOK', 'description' => 'Voir Cahier de Notes', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'EDIT_GRADEBOOK', 'description' => 'Modifier Cahier de Notes', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'TAKE_ATTENDANCE', 'description' => 'Prendre Présence', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'VIEW_ATTENDANCE', 'description' => 'Voir Présence', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'MANAGE_STUDENTS', 'description' => 'Gérer Étudiants', 'status' => 'active']);
        Right::updateOrCreate(['code' => 'MANAGE_TEACHERS', 'description' => 'Gérer Enseignants', 'status' => 'active']);

        Title::updateOrCreate(['code' => 'DIR', 'title' => 'Directeur', 'description' => 'Directeur']);
        Title::updateOrCreate(['code' => 'DEP', 'title' => 'Chef département', 'description' => 'Chef']);
        Title::updateOrCreate(['code' => 'CP', 'title' => 'Professeur principal', 'description' => 'Prof principal']);

        //$this->command->info('Rights and Titles created');
    }
}
