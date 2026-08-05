<?php

namespace Tests\Unit;

use App\Core\Authorization\Models\Right;
use App\Core\UserManagement\Models\ParentModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RemainingRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_right_relations(): void
    {
        $right = Right::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $right->users());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $right->employeeTypes());
    }

    public function test_parent_secondary_user_relation(): void
    {
        $parent = ParentModel::factory()->create();

        $this->assertNull($parent->secondaryUser()->first());
    }
}