<?php

namespace TomatoPHP\FilamentIssues\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use TomatoPHP\FilamentIssues\Exceptions\GitHubRateLimitException;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Models\IssueOwner;
use TomatoPHP\FilamentIssues\Models\Label;
use TomatoPHP\FilamentIssues\Models\Org;
use TomatoPHP\FilamentIssues\Models\Reaction;
use TomatoPHP\FilamentIssues\Models\Repository;

class FilamentIssuesServices
{
    private array $repos = [];

    public function register(string | array | Closure $repo): void
    {
        if ($repo instanceof Closure) {
            $repo = call_user_func($repo);
        }

        if (is_array($repo)) {
            foreach ($repo as $r) {
                $this->register($r);
            }
        } else {
            $repo = str($repo)->explode('/');
            if (! isset($this->repos[$repo[0]])) {
                $this->repos[$repo[0]] = [];
            }
            $this->repos[$repo[0]][] = $repo[1];
        }
    }

    public function getRepos(): array
    {
        return $this->repos;
    }

    public function repos(): RepoService
    {
        return new RepoService;
    }

    public function issues(): IssueService
    {
        return new IssueService;
    }

    public function load(): array
    {
        return $this->issues()->getAll();
    }

    public function publicIssues(Request $request): Builder
    {
        $query = Issue::query()->where('is_public', 1);

        if ($request->has('search') && ! empty($request->get('search'))) {
            $query->where('title', 'like', '%' . $request->get('search') . '%');
        }

        if ($request->has('sort') && ! empty($request->get('sort'))) {
            if ($request->get('sort') === 'popular') {
                // Order By Type where type is 'verified', 'public'
                $query->orderBy('is_trend');
            } elseif ($request->get('sort') === 'recent') {
                $query->orderBy('createdAt', 'desc');
            } elseif ($request->get('sort') === 'alphabetical') {
                $query->orderBy('title');
            } else {
                $query->inRandomOrder();
            }
        }

        if ($request->has('repo') && ! empty($request->get('repo'))) {
            $name = str($request->get('repo'))->explode('/');
            $org = Org::query()->where('name', $name[0])->first();
            if ($org) {
                $repo = Repository::query()->where('name', $name[1])->where('owner_id', $org->id)->first();
                if ($repo) {
                    $query->where('repo_id', $repo->id);
                }
            }
        }

        return $query;
    }

    /**
     * @throws GitHubRateLimitException
     */
    public function fetchRepo(Repository $repo): array
    {
        return $this->issues()->getIssuesForRepo($repo);
    }

    public function refresh(): void
    {
        $this->load();
    }

    public function clear(): void
    {
        Issue::query()->delete();
        Repository::query()->delete();
        Org::query()->delete();
        IssueOwner::query()->delete();
        Label::query()->delete();
        Reaction::query()->delete();
    }
}
