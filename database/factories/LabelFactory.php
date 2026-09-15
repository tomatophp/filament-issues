<?php

namespace TomatoPHP\FilamentIssues\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentIssues\Models\Label;

/**
 * @extends Factory<Label>
 */
class LabelFactory extends Factory
{
    protected $model = Label::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['bug', 'enhancement', 'help wanted', 'documentation']),
            'color' => $this->faker->hexColor(),
        ];
    }
}
