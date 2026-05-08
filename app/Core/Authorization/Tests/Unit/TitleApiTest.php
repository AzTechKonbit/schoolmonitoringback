<?php

namespace App\Core\Authorization\Tests\Unit;

use App\Core\Authorization\Models\Title;
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
    }

    public function test_can_list_titles(): void
    {
        Title::factory(5)->create();
        $response = $this->getJson('/titles');
        $response->assertStatus(200);
    }
}
