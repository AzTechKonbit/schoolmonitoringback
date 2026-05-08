<?php

namespace App\Core\Authorization\Database\Factories;

use App\Core\Authorization\Models\{Right, Title};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RightFactory extends Factory
{
    protected $model = Right::class;

    public function definition(): array
    {
        return [
            'code' => 'RIGHT_' . strtoupper(Str::random(5)),
            'description' => 'Description du droit',
            'status' => 'active',
        ];
    }
}
