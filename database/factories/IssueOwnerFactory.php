<?php

namespace TomatoPHP\FilamentIssues\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentIssues\Models\IssueOwner;

/**
 * @extends Factory<IssueOwner>
 */
class IssueOwnerFactory extends Factory
{
    protected $model = IssueOwner::class;

    public function definition(): array
    {
        $login = $this->faker->unique()->userName();

        return [
            'name' => $login,
            'url' => 'https://github.com/' . $login,
            'profilePictureUrl' => 'https://avatars.githubusercontent.com/u/' . $this->faker->numberBetween(1, 999999) . '?s=48',
        ];
    }
}
