<?php

use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Http;
use TomatoPHP\FilamentIssues\Database\Factories\IssueFactory;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\Pages\ListIssues;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::query()->create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secret']));
});

it('renders the issues page over HTTP', function () {
    IssueFactory::new()->count(2)->create();

    $this->get(IssueResource::getUrl('index'))->assertSuccessful();
});

it('lists the synced issues', function () {
    $issues = IssueFactory::new()->count(3)->create();

    livewire(ListIssues::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($issues);
});

it('filters pull requests', function () {
    $issue = IssueFactory::new()->create();
    $pullRequest = IssueFactory::new()->pullRequest()->create();

    livewire(ListIssues::class)
        ->filterTable('isPullRequest', true)
        ->assertCanSeeTableRecords([$pullRequest])
        ->assertCanNotSeeTableRecords([$issue]);
});

it('toggles an issue public', function () {
    $issue = IssueFactory::new()->private()->create();

    livewire(ListIssues::class)
        ->call('updateTableColumnState', 'is_public', (string) $issue->getKey(), true);

    expect($issue->refresh()->is_public)->toBeTrue();
});

it('cleans the synced issues from the header action', function () {
    IssueFactory::new()->count(2)->create();

    livewire(ListIssues::class)
        ->callAction(TestAction::make('clean'))
        ->assertNotified();

    expect(Issue::query()->count())->toBe(0);
});

it('refreshes issues from the header action', function () {
    config()->set('filament-issues.repos', ['tomatophp' => ['filament-issues']]);
    Http::preventStrayRequests();
    Http::fake(['api.github.com/*' => Http::response([])]);

    livewire(ListIssues::class)
        ->callAction(TestAction::make('refresh'))
        ->assertNotified();

    Http::assertSentCount(1);
});
