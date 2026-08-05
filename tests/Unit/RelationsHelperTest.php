<?php

namespace Tests\Unit;

use App\Core\UserManagement\Models\Student;
use App\Relations\ModuleRelations;
use App\Relations\RelationRegistry;
use PHPUnit\Framework\TestCase;

class RelationsHelperTest extends TestCase
{
    public function test_relation_registry_register_and_exists(): void
    {
        RelationRegistry::register('ModelX', 'relationA');
        $this->assertTrue(RelationRegistry::exists('ModelX', 'relationA'));
        $this->assertFalse(RelationRegistry::exists('ModelX', 'unknown'));
        $this->assertFalse(RelationRegistry::exists('Unknown', 'any'));
    }

    public function test_relation_registry_exists_for_missing_model(): void
    {
        RelationRegistry::register('SomeModel', 'rel');
        $this->assertTrue(RelationRegistry::exists('SomeModel', 'rel'));
        RelationRegistry::register('SomeModel', 'rel2');
        $this->assertTrue(RelationRegistry::exists('SomeModel', 'rel2'));
    }

    public function test_module_relations_filters_to_only_registered(): void
    {
        RelationRegistry::register(Student::class, 'user');
        RelationRegistry::register(Student::class, 'parent');

        $result = ModuleRelations::onlyExisting(Student::class, ['user', 'parent', 'classes', 'ghost']);

        $this->assertContains('user', $result);
        $this->assertContains('parent', $result);
        $this->assertNotContains('ghost', $result);
    }

    public function test_module_relations_returns_empty_when_none_match(): void
    {
        $result = ModuleRelations::onlyExisting(Student::class, ['nonexistent']);
        $this->assertEquals([], $result);
    }
}