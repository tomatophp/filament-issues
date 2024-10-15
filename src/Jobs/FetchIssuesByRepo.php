<?php


namespace TomatoPHP\FilamentIssues\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use TomatoPHP\FilamentIssues\Facades\FilamentIssues;
use TomatoPHP\FilamentIssues\Models\Repository;

class FetchIssuesByRepo implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function __construct(
        private Repository $repo
    )
    {
        //
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        FilamentIssues::fetchRepo($this->repo);
    }
}
