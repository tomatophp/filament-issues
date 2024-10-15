<?php

namespace TomatoPHP\FilamentIssues\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use TomatoPHP\FilamentIssues\Exceptions\GitHubRateLimitException;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Models\Org;
use TomatoPHP\FilamentIssues\Models\Repository;

class FilamentIssuesServices
{
    /**
     * @return RepoService
     */
    public function repos(): RepoService
    {
        return new RepoService();
    }

    /**
     * @return IssueService
     */
    public function issues(): IssueService
    {
        return new IssueService();
    }


    /**
     * @return array
     */
    public function load(): array
    {
       return $this->issues()->getAll();
    }


    /**
     * @param Request $request
     * @return Builder
     */
    public function publicIssues(Request $request): Builder
    {
        $query = Issue::query()->where('is_public', 1);

        if($request->has('search') && !empty($request->get('search'))){
            $query->where('title', 'like', '%'.$request->get('search').'%');
        }

        if($request->has('sort') && !empty($request->get('sort'))){
            if($request->get('sort') === 'popular'){
                //Order By Type where type is 'verified', 'public'
                $query->orderBy("is_trend");
            }
            elseif ($request->get('sort') === 'recent'){
                $query->orderBy('createdAt', 'desc');
            }
            elseif ($request->get('sort') === 'alphabetical'){
                $query->orderBy('title');
            }
            else {
                $query->inRandomOrder();
            }
        }

        if($request->has('repo') && !empty($request->get('repo'))){
            $name = str($request->get('repo'))->explode("/");
            $org = Org::query()->where('name', $name[0])->first();
            if($org){
                $repo = Repository::query()->where('name', $name[1])->where('owner_id', $org->id)->first();
                if($repo){
                    $query->where('repo_id', $repo->id);
                }
            }
        }

        return $query;
    }


    /**
     * @param Repository $repo
     * @return array
     * @throws GitHubRateLimitException
     */
    public function fetchRepo(Repository $repo): array
    {
        return $this->issues()->getIssuesForRepo($repo);
    }

    /**
     * @return void
     */
    public function refresh(): void
    {
        $this->load();
    }
}
