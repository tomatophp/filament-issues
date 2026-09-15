<?php

namespace TomatoPHP\FilamentIssues\Facades;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use TomatoPHP\FilamentIssues\Models\Repository;
use TomatoPHP\FilamentIssues\Services\IssueService;
use TomatoPHP\FilamentIssues\Services\RepoService;

/**
 * @method static RepoService repos()
 * @method static IssueService issues()
 * @method static array load()
 * @method static void refresh()
 * @method static array fetchRepo(Repository $repo)
 * @method static Builder publicIssues(Request $request)
 * @method static void register(string|array|Closure $repo)
 * @method static array getRepos()
 * @method static void clear()
 */
class FilamentIssues extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-issues';
    }
}
