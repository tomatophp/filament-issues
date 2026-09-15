<?php

declare(strict_types=1);

namespace TomatoPHP\FilamentIssues\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use TomatoPHP\FilamentIssues\Clients\GitHub;
use TomatoPHP\FilamentIssues\Exceptions\GitHubRateLimitException;
use TomatoPHP\FilamentIssues\Exceptions\RepoNotCrawlableException;
use TomatoPHP\FilamentIssues\Facades\FilamentIssues;
use TomatoPHP\FilamentIssues\Models\Org;
use TomatoPHP\FilamentIssues\Models\Repository;

final readonly class RepoService
{
    /**
     * Every configured, registered and organization repository, created in the database once.
     *
     * @return Collection<int, Repository>
     */
    public function reposToCrawl(): Collection
    {
        $repositories = collect();

        foreach ($this->repoNamesByOwner() as $owner => $repoNames) {
            $org = Org::query()->firstOrCreate(['name' => $owner], ['last_update' => now()]);

            foreach (array_unique($repoNames) as $repoName) {
                $repositories->push(
                    Repository::query()->firstOrCreate(['owner_id' => $org->id, 'name' => $repoName])
                );
            }
        }

        return $repositories->unique('id')->values();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function repoNamesByOwner(): array
    {
        $names = [];

        $sources = [
            (array) config('filament-issues.repos', []),
            FilamentIssues::getRepos(),
            $this->fetchReposFromOrgs(),
        ];

        foreach ($sources as $source) {
            foreach ($source as $owner => $repoNames) {
                // Accept both ['owner' => ['repo']] and ['owner/repo'].
                if (is_int($owner) && is_string($repoNames) && str_contains($repoNames, '/')) {
                    [$owner, $repoNames] = explode('/', $repoNames, 2);
                }

                $names[$owner] = array_values(array_merge($names[$owner] ?? [], (array) $repoNames));
            }
        }

        return $names;
    }

    /**
     * @throws GitHubRateLimitException
     * @throws RepoNotCrawlableException
     */
    public function ensureRepoCanBeCrawled(Repository $repository): void
    {
        $repositoryData = $this->getRepoFromGitHubApi($repository);

        if ($this->repoIsArchived($repositoryData)) {
            throw new RepoNotCrawlableException("Repository {$repository->repo} is archived.");
        }
    }

    private function repoIsArchived(array $repoData): bool
    {
        return $repoData['archived'] ?? true;
    }

    /**
     * @throws GitHubRateLimitException
     * @throws RepoNotCrawlableException
     */
    private function getRepoFromGitHubApi(Repository $repo): array
    {
        $fullRepoName = $repo->repo;

        $result = app(GitHub::class)
            ->client()
            ->get('repos/' . $fullRepoName);

        if (! $result->successful()) {
            $this->handleUnsuccessfulRequest($result, $fullRepoName);
        }

        return $result->json();
    }

    /**
     * @throws GitHubRateLimitException
     * @throws RepoNotCrawlableException
     */
    private function handleUnsuccessfulRequest(Response $response, string $fullRepoName): void
    {
        match ($response->status()) {
            404 => throw new RepoNotCrawlableException($fullRepoName . ' is not a valid GitHub repo.'),
            403 => $this->handleForbiddenResponse($response, $fullRepoName),
            default => throw new RepoNotCrawlableException('Unknown error for repo ' . $fullRepoName),
        };
    }

    /**
     * @throws GitHubRateLimitException
     * @throws RepoNotCrawlableException
     */
    private function handleForbiddenResponse(Response $response, string $fullRepoName): void
    {
        if ($response->header('X-RateLimit-Remaining') === '0') {
            throw new GitHubRateLimitException('GitHub API rate limit reached!');
        }

        throw new RepoNotCrawlableException($fullRepoName . ' is a forbidden GitHub repo.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function fetchReposFromOrgs(): array
    {
        return collect((array) config('filament-issues.orgs', []))
            ->mapWithKeys(fn (string $org): array => [$org => $this->fetchReposFromOrg($org)])
            ->all();
    }

    /**
     * Fetch the crawlable (not archived) repos of a GitHub organization.
     * Stops at the first empty or unsuccessful page, so an unknown organization
     * or a rate limited token can never loop forever.
     *
     * @return array<int, string>
     */
    private function fetchReposFromOrg(string $org): array
    {
        $existing = Org::query()->where('name', $org)->first();

        if ($existing) {
            return $existing->repositories()->pluck('name')->all();
        }

        $client = app(GitHub::class)->client();
        $repos = [];

        for ($page = 1; $page <= 50; $page++) {
            $response = $client->get("orgs/{$org}/repos", ['per_page' => 100, 'type' => 'sources', 'page' => $page]);
            $result = $response->json();

            if (! $response->successful() || ! is_array($result) || ! array_is_list($result) || $result === []) {
                break;
            }

            foreach ($result as $repo) {
                if (is_array($repo) && isset($repo['name']) && ! $this->repoIsArchived($repo)) {
                    $repos[] = $repo['name'];
                }
            }
        }

        return $repos;
    }
}
