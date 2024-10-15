<?php

namespace TomatoPHP\FilamentIssues\Facades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use TomatoPHP\FilamentIssues\Models\Repository;
use TomatoPHP\FilamentIssues\Services\RepoService;

/**
 * @method static RepoService repos()
 * @method static RepoService issues()
 * @method static array load()
 * @method static void refresh()
 * @method static array fetchRepo(Repository $repo)
 * @method static Builder publicIssues(Request $request)
 */
class FilamentIssues extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-issues';
    }
}
