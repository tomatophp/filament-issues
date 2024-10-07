<?php

declare(strict_types=1);

namespace TomatoPHP\FilamentIssues\Jobs;

use TomatoPHP\FilamentIssues\DataTransferObjects\Repository;
use TomatoPHP\FilamentIssues\Services\IssueService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

final class PreloadIssuesForRepos implements ShouldQueue
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

    public function handle(IssueService $issueService): void
    {
        foreach ($this->repos as $repo) {
            $issueService->getIssuesForRepo(repo: $repo, forceRefresh: true);
        }
    }
}
