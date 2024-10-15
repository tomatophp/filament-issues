<?php

declare(strict_types=1);

namespace TomatoPHP\FilamentIssues\Jobs;

use TomatoPHP\FilamentIssues\Exceptions\GitHubRateLimitException;
use TomatoPHP\FilamentIssues\Models\Repository;
use TomatoPHP\FilamentIssues\Exceptions\RepoNotCrawlableException;
use TomatoPHP\FilamentIssues\Services\RepoService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

final class EnsureRepoIsCrawlable implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  Collection<Repository>  $repos
     */
    public function __construct(private Collection $repos)
    {
        //
    }

    /**
     * @param RepoService $repoService
     * @return void
     * @throws GitHubRateLimitException
     */
    public function handle(RepoService $repoService): void
    {
        foreach ($this->repos as $repo) {
            try {
                $repoService->ensureRepoCanBeCrawled($repo);
            } catch (RepoNotCrawlableException $e) {
                report($e);
            }
        }
    }
}
