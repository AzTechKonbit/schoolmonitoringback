<?php

namespace App\Core\UserManagement\Tests\Unit;

use App\Core\Models\School;
use App\Core\UserManagement\Models\ParentModel;
use App\Core\UserManagement\Models\Student;
use Tests\TestControllerCase;

class StudentApiTest extends TestControllerCase
{

    public function test_can_create_student(): void
    {

        $response = $this->postJson('/students', [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'pierre@test.com',
            'password' => 'password123',
            'gender' => 'M',
            'school_id' => School::factory()->create()->getKey(),
            'parent_id' => ParentModel::factory()->create()->getKey(),
            'dob' => '2015-05-20',
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_students(): void
    {
        Student::factory(20)->create();
        $response = $this->getJson('/students');

        $response->assertStatus(200);
    }


}
