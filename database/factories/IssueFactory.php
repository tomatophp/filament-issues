<?php

namespace TomatoPHP\FilamentIssues\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Models\Repository;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        $number = $this->faker->unique()->numberBetween(1, 99999);

        return [
            'issue_id' => (string) $this->faker->unique()->numberBetween(100000000, 999999999),
            'repo_id' => RepositoryFactory::new(),
            'createdBy' => IssueOwnerFactory::new(),
            'number' => $number,
            'repoName' => fn (array $attributes): string => Repository::query()->find($attributes['repo_id'])?->repo ?? 'tomatophp/demo',
            'repoUrl' => fn (array $attributes): string => 'https://github.com/' . $attributes['repoName'],
            'title' => rtrim($this->faker->sentence(6), '.'),
            'url' => fn (array $attributes): string => $attributes['repoUrl'] . '/issues/' . $number,
            'body' => $this->faker->paragraph(),
            'commentCount' => $this->faker->numberBetween(0, 12),
            'createdAt' => $this->faker->dateTimeBetween('-3 months'),
            'isPullRequest' => false,
            'is_public' => true,
            'is_trend' => false,
        ];
    }

    public function pullRequest(): static
    {
        return $this->state(['isPullRequest' => true]);
    }

    public function private(): static
    {
        return $this->state(['is_public' => false]);
    }
}
