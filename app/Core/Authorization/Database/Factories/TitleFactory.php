<?php

namespace App\Core\Authorization\Database\Factories;

use App\Core\Authorization\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class TitleFactory extends Factory
{
    protected $model = Title::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(3)),
            'title' => 'Titre ' . $this->faker->jobTitle(),
            'description' => 'Description',
        ];
    }
}
