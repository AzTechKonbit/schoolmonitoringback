<?php

namespace Modules\Academic\Database\Factories;

use App\Core\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academic\Models\Term;

class TermFactory extends Factory
{
    protected $model = Term::class;

    public function definition(): array
    {
        return [
            'term' => 'Trimestre ' . rand(1, 3),
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth()->addMonths(3),
            'school_id' => School::factory(),
        ];
    }
}
