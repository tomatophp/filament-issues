<?php

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use TomatoPHP\FilamentIssues\Facades\FilamentIssues;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Models\IssueOwner;
use TomatoPHP\FilamentIssues\Models\Label;
use TomatoPHP\FilamentIssues\Models\Repository;
use TomatoPHP\FilamentIssues\Services\RepoService;

function fakeGitHubIssues(int $thumbsUp = 3): void
{
    Http::preventStrayRequests();
    Http::fake([
        'api.github.com/repos/tomatophp/filament-issues/issues*' => Http::response([
            [
                'id' => 1001,
                'number' => 7,
                'title' => 'Refresh fails on archived repos',
                'html_url' => 'https://github.com/tomatophp/filament-issues/issues/7',
                'body' => 'Steps to reproduce...',
                'comments' => 2,
                'created_at' => '2026-09-01T10:00:00Z',
                'user' => ['login' => 'octocat', 'html_url' => 'https://github.com/octocat', 'avatar_url' => 'https://avatars.githubusercontent.com/u/1'],
                'labels' => [['name' => 'bug', 'color' => 'd73a4a']],
                'reactions' => ['+1' => $thumbsUp, 'heart' => 1, 'total_count' => $thumbsUp + 1],
            ],
            [
                'id' => 1002,
                'number' => 8,
                'title' => 'Add a dark cover',
                'html_url' => 'https://github.com/tomatophp/filament-issues/pull/8',
                'body' => null,
                'comments' => 0,
                'created_at' => '2026-09-02T10:00:00Z',
                'user' => ['login' => 'octocat', 'html_url' => 'https://github.com/octocat', 'avatar_url' => 'https://avatars.githubusercontent.com/u/1?v=4'],
                'labels' => [],
                'reactions' => ['+1' => 0],
                'pull_request' => ['url' => 'https://api.github.com/repos/tomatophp/filament-issues/pulls/8'],
            ],
        ]),
    ]);
}

beforeEach(function () {
    config()->set('filament-issues.repos', ['tomatophp' => ['filament-issues']]);
});

it('fetches issues, pull requests, owners, labels and reactions from GitHub', function () {
    fakeGitHubIssues();

    FilamentIssues::refresh();

    expect(Issue::query()->count())->toBe(2)
        ->and(Repository::query()->first()->repo)->toBe('tomatophp/filament-issues')
        ->and(IssueOwner::query()->count())->toBe(1)
        ->and(IssueOwner::query()->first()->profilePictureUrl)->toBe('https://avatars.githubusercontent.com/u/1?s=48')
        ->and(Label::query()->first()->color)->toBe('#d73a4a');

    $issue = Issue::query()->where('number', 7)->first();

    expect($issue->isPullRequest)->toBeFalse()
        ->and($issue->repoName)->toBe('tomatophp/filament-issues')
        ->and($issue->labels->pluck('name')->all())->toBe(['bug'])
        ->and($issue->reactions->firstWhere('content', '+1')->pivot->count)->toBe(3)
        ->and(Issue::query()->where('number', 8)->first()->isPullRequest)->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.github.com/repos/tomatophp/filament-issues/issues');
});

it('does not duplicate reactions when refreshing twice', function () {
    fakeGitHubIssues(thumbsUp: 3);
    FilamentIssues::refresh();

    // Http::fake() keeps the first matching stub, so start from a fresh client factory.
    Http::swap(new Factory);
    fakeGitHubIssues(thumbsUp: 5);
    FilamentIssues::refresh();

    $issue = Issue::query()->where('number', 7)->first();

    expect(Issue::query()->count())->toBe(2)
        ->and(DB::table('git_issues_has_reactions')->where('issue_id', $issue->id)->count())->toBe(2)
        ->and($issue->reactions()->where('content', '+1')->first()->pivot->count)->toBe(5);
});

it('fetches each configured repository once', function () {
    config()->set('filament-issues.repos', ['tomatophp' => ['filament-issues', 'filament-issues']]);
    FilamentIssues::register('tomatophp/filament-issues');
    fakeGitHubIssues();

    FilamentIssues::refresh();

    expect(Repository::query()->count())->toBe(1);
    Http::assertSentCount(1);
});

it('uses the labels from the filament-issues config', function () {
    fakeGitHubIssues();
    config()->set('filament-issues.labels', ['bug']);

    $repository = app(RepoService::class)->reposToCrawl()->first();
    $included = FilamentIssues::fetchRepo($repository);

    expect(collect($included)->pluck('number')->all())->toBe([7]);
});

it('stops crawling an organization when GitHub answers with an error', function () {
    config()->set('filament-issues.repos', []);
    config()->set('filament-issues.orgs', ['missing-org']);
    Http::preventStrayRequests();
    Http::fake([
        'api.github.com/orgs/missing-org/repos*' => Http::response(['message' => 'Not Found'], 404),
    ]);

    FilamentIssues::refresh();

    Http::assertSentCount(1);
    expect(Repository::query()->count())->toBe(0);
});

it('crawls every page of an organization and skips archived repos', function () {
    config()->set('filament-issues.repos', []);
    config()->set('filament-issues.orgs', ['tomatophp']);
    Http::preventStrayRequests();
    Http::fake([
        'api.github.com/orgs/tomatophp/repos*' => Http::sequence()
            ->push([['name' => 'filament-issues', 'archived' => false], ['name' => 'old-thing', 'archived' => true]])
            ->push([]),
        'api.github.com/repos/*' => Http::response([]),
    ]);

    FilamentIssues::refresh();

    expect(Repository::query()->pluck('name')->all())->toBe(['filament-issues']);
});

it('clears every synced record', function () {
    fakeGitHubIssues();
    FilamentIssues::refresh();

    FilamentIssues::clear();

    expect(Issue::query()->count())->toBe(0)
        ->and(Repository::query()->count())->toBe(0)
        ->and(Label::query()->count())->toBe(0);
});

it('refreshes issues from the artisan command', function () {
    fakeGitHubIssues();

    $this->artisan('filament-issues:refresh')->assertSuccessful();

    expect(Issue::query()->count())->toBe(2);
});

it('sends basic auth when a GitHub token is configured', function () {
    config()->set('services.github.username', 'tomato');
    config()->set('services.github.token', 'secret');
    fakeGitHubIssues();

    FilamentIssues::refresh();

    Http::assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Basic ' . base64_encode('tomato:secret')));
});
