<?php

namespace App\Core\Authorization\Tests\Unit;

use App\Core\Authorization\Models\Title;
use App\Core\Authorization\Services\AuthorizationService;
use App\Core\Models\User;
use App\Core\UserManagement\Models\Employee;
use Tests\TestControllerCase;

class TitleApiTest extends TestControllerCase
{
    public function test_can_create_title(): void
    {
        $response = $this->postJson('/titles', [
            'code' => 'DIR',
            'title' => 'Directeur',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('titles', ['code' => 'DIR']);
    }

    public function test_can_list_titles(): void
    {
        Title::factory(5)->create();
        $response = $this->getJson('/titles');
        $response->assertStatus(200);
    }

    public function test_can_search_titles(): void
    {
        Title::factory()->create(['title' => 'Directeur Général']);

        $response = $this->getJson('/titles?search=Directeur');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Directeur Général']);
    }

    public function test_can_update_title(): void
    {
        $title = Title::factory()->create(['code' => 'OLD']);

        $response = $this->putJson("/titles/{$title->id}", [
            'code' => 'NEW',
            'title' => 'Nouveau Titre',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('titles', ['id' => $title->id, 'title' => 'Nouveau Titre']);
    }

    public function test_update_non_existent_title_returns_404(): void
    {
        $this->putJson('/titles/99999', ['title' => 'x'])->assertStatus(404);
    }

    public function test_can_destroy_title(): void
    {
        $title = Title::factory()->create();

        $this->deleteJson("/titles/{$title->id}")->assertStatus(200);

        $this->assertDatabaseMissing('titles', ['id' => $title->id]);
    }

    public function test_destroy_non_existent_title_returns_404(): void
    {
        $this->deleteJson('/titles/99999')->assertStatus(404);
    }

    public function test_can_assign_title_to_employee(): void
    {
        $employee = Employee::factory()->create();
        $title = Title::factory()->create();

        $response = $this->postJson("/employees/{$employee->id}/titles", [
            'title_id' => $title->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('employee_titles', ['employee_id' => $employee->id, 'title_id' => $title->id]);
    }

    public function test_can_remove_title_from_employee(): void
    {
        $employee = Employee::factory()->create();
        $title = Title::factory()->create();
        $employee->titles()->attach($title->id);

        $response = $this->deleteJson("/employees/{$employee->id}/titles", [
            'title_id' => $title->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('employee_titles', ['employee_id' => $employee->id, 'title_id' => $title->id]);
    }

    public function test_assign_title_to_non_existent_employee_returns_404(): void
    {
        $this->postJson('/employees/99999/titles', ['title_id' => 1])->assertStatus(404);
    }

    public function test_remove_title_from_non_existent_employee_returns_404(): void
    {
        $this->deleteJson('/employees/99999/titles', ['title_id' => 1])->assertStatus(404);
    }

    public function test_authorization_service_user_rights_flows(): void
    {
        $service = app(AuthorizationService::class);
        $user = User::factory()->create();
        $right = \App\Core\Authorization\Models\Right::factory()->create(['code' => 'CAN_FLY']);

        $this->assertFalse($service->hasRight($user, 'CAN_FLY'));

        $service->assignRightToUser($user, $right->id);
        $this->assertTrue($service->hasRight($user, 'CAN_FLY'));
        $this->assertTrue($service->getUserRights($user)->contains('id', $right->id));

        $service->syncUserRights($user, []);
        $this->assertFalse($service->hasRight($user, 'CAN_FLY'));

        $service->assignRightToUser($user, $right->id);
        $service->removeRightFromUser($user, $right->id);
        $this->assertFalse($service->hasRight($user, 'CAN_FLY'));
    }

    public function test_authorization_service_title_flows(): void
    {
        $service = app(AuthorizationService::class);

        $right = $service->createRight(['code' => 'CODE_R', 'description' => 'd', 'status' => 'active']);
        $this->assertDatabaseHas('rights', ['id' => $right->id]);

        $right = $service->updateRight($right, ['description' => 'updated']);
        $this->assertEquals('updated', $right->description);

        $this->assertTrue($service->deleteRight($right));

        $title = $service->createTitle(['code' => 'T1', 'title' => 'Titre', 'description' => null]);
        $this->assertDatabaseHas('titles', ['id' => $title->id]);

        $title = $service->updateTitle($title, ['title' => 'Updated']);
        $this->assertEquals('Updated', $title->title);

        $this->assertTrue($service->deleteTitle($title));

        $employee = Employee::factory()->create();
        $title2 = Title::factory()->create();
        $service->assignTitleToEmployee($employee, $title2->id);
        $this->assertDatabaseHas('employee_titles', ['employee_id' => $employee->id, 'title_id' => $title2->id]);

        $service->assignTitleToEmployee($employee, $title2->id);
        $this->assertDatabaseHas('employee_titles', ['employee_id' => $employee->id, 'title_id' => $title2->id]);

        $service->removeTitleFromEmployee($employee, $title2->id);
        $this->assertDatabaseMissing('employee_titles', ['employee_id' => $employee->id, 'title_id' => $title2->id]);
    }

    public function test_authorization_service_group_right_flows(): void
    {
        $service = app(AuthorizationService::class);
        $type = \App\Core\UserManagement\Models\EmployeeType::factory()->create();
        $right = \App\Core\Authorization\Models\Right::factory()->create();

        $service->assignRightToGroup($type, $right->id);
        $this->assertDatabaseHas('group_rights', ['employee_type_id' => $type->id, 'right_id' => $right->id]);

        $service->removeRightFromGroup($type, $right->id);
        $this->assertDatabaseMissing('group_rights', ['employee_type_id' => $type->id, 'right_id' => $right->id]);
    }
}