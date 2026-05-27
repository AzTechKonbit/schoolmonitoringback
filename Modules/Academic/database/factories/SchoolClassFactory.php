<?php

namespace Modules\Academic\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academic\Models\SchoolClass;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        return [
            'name' => collect(['CP', 'CE1', 'CE2', 'CM1', 'CM2'])->random(),
        ];
    }
}
