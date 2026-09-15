<?php

namespace TomatoPHP\FilamentIssues\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentIssues\Models\Repository;

/**
 * @extends Factory<Repository>
 */
class RepositoryFactory extends Factory
{
    protected $model = Repository::class;

    public function definition(): array
    {
        return [
            'owner_id' => OrgFactory::new(),
            'name' => $this->faker->unique()->slug(2),
        ];
    }
}
