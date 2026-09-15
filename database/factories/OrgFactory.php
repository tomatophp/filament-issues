<?php

namespace TomatoPHP\FilamentIssues\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentIssues\Models\Org;

/**
 * @extends Factory<Org>
 */
class OrgFactory extends Factory
{
    protected $model = Org::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->slug(2),
            'last_update' => now(),
        ];
    }
}
