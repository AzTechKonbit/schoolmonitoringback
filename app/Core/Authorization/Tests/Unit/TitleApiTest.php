<?php

namespace App\Core\Authorization\Tests\Unit;

use App\Core\Authorization\Models\Title;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TitleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_title(): void
    {
        $response = $this->postJson('/api/titles', [
            'code' => 'DIR',
            'title' => 'Directeur',
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_titles(): void
    {
        Title::factory(5)->create();

        $response = $this->getJson('/api/titles');

        $response->assertStatus(200);
    }
}
