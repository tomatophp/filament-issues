<?php

declare(strict_types=1);

namespace TomatoPHP\FilamentIssues\Services;

use TomatoPHP\FilamentIssues\Clients\GitHub;
use TomatoPHP\FilamentIssues\DataTransferObjects\Repository;
use TomatoPHP\FilamentIssues\Exceptions\GitHubRateLimitException;
use TomatoPHP\FilamentIssues\Exceptions\RepoNotCrawlableException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use TomatoPHP\FilamentIssues\Models\Org;

final readonly class RepoService
{
    public function reposToCrawl(): Collection
    {
        return collect(config('filament-issues.repos'))
            ->merge($this->fetchReposFromOrgs())
            ->flatMap(function (array $repoNames, string $owner): Collection {
                foreach ($repoNames as $repoName){
                    $org = Org::query()->where('name', $owner)->first();
                    if(!$org){
                        $org = Org::query()->create([
                            'name' => $owner,
                            'last_update' => now()
                        ]);
                    }

                    $org = $org->id;

                    $repo = \TomatoPHP\FilamentIssues\Models\Repository::query()
                        ->where('owner_id', $org)
                        ->where('name', $repoName)
                        ->exists();

                    if(!$repo){
                        $repo = new \TomatoPHP\FilamentIssues\Models\Repository();
                        $repo->owner_id = $org;
                        $repo->name = $repoName;
                        $repo->save();
                    }
                }

                return \TomatoPHP\FilamentIssues\Models\Repository::all();
            });
    }

    /**
     * @throws GitHubRateLimitException
     * @throws RepoNotCrawlableException
     */
    public function ensureRepoCanBeCrawled(Repository $repository): void
    {
        $repositoryData = $this->getRepoFromGitHubApi($repository);

        if ($this->repoIsArchived($repositoryData)) {
            throw new RepoNotCrawlableException(
                "Repository {$repository->owner}/{$repository->name} is archived."
            );
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
        $fullRepoName = $repo->owner.'/'.$repo->name;

        $result = app(GitHub::class)
            ->client()
            ->get('repos/'.$fullRepoName);

        if (! $result->successful()) {
            $this->handleUnsuccessfulIssueRequest($result, $fullRepoName);
        }

        return $result->json();
    }

    /**
     * @throws GitHubRateLimitException
     * @throws RepoNotCrawlableException
     */
    private function handleUnsuccessfulIssueRequest(Response $response, string $fullRepoName): void
    {
        match ($response->status()) {
            404 => $this->handleNotFoundResponse($fullRepoName),
            403 => $this->handleForbiddenResponse($response, $fullRepoName),
            default => throw new RepoNotCrawlableException('Unknown error for repo '.$fullRepoName),
        };
    }

    /**
     * @throws RepoNotCrawlableException
     */
    private function handleNotFoundResponse(string $fullRepoName): void
    {
        throw new RepoNotCrawlableException($fullRepoName.' is not a valid GitHub repo.');
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

        throw new RepoNotCrawlableException($fullRepoName.' is a forbidden GitHub repo.');
    }

    private function fetchReposFromOrgs(): Collection
    {
        return collect(config('filament-issues.orgs'))
            ->mapWithKeys(fn (string $org): array => [$org => $this->fetchReposFromOrg($org)]);
    }

    /**
     * Fetch all the crawlable repos for a GitHub organization.
     */
    private function fetchReposFromOrg(string $org): ?array
    {
        $checkExistsOrg = Org::query()->where('name', $org)->exists();
        if(!$checkExistsOrg){
            $owner = Org::query()->create([
                'name' => $org,
                'last_update' => now()
            ]);

            $client = app(GitHub::class)->client();
            $page = 1;

            $repos = collect();

            while ($result = $client->get("orgs/{$org}/repos", ['per_page' => 100, 'type' => 'sources', 'page' => $page])->json()) {
                $repoNames = collect($result)
                    ->reject(function (array $repo) use ($owner){
                        return ($this->repoIsArchived($repo) || \TomatoPHP\FilamentIssues\Models\Repository::query()
                                ->where('owner_id', $owner->id)
                                ->where('name', $repo['name'])
                                ->exists());
                    })
                    ->pluck('name');

                $repos->push(...$repoNames);

                $page++;
            }

            return $repos->all();
        }
        else {
            return Org::query()->where('name', $org)->first()?->repositories->pluck('name')->toArray();
        }
    }
}
