<?php

use Illuminate\Support\Facades\Blade;
use TomatoPHP\FilamentIssues\Database\Factories\IssueFactory;

it('renders only public issues in the public component', function () {
    $public = IssueFactory::new()->create(['title' => 'Visible public issue']);
    IssueFactory::new()->private()->create(['title' => 'Hidden private issue']);

    $html = Blade::render('<x-filament-issues />');

    expect($html)->toContain('Visible public issue')
        ->not->toContain('Hidden private issue')
        ->toContain('Search issues...')
        ->not->toContain('cms::messages');
});

it('renders a single issue card', function () {
    $issue = IssueFactory::new()->create(['title' => 'Card issue']);

    $html = Blade::render('<x-filament-issue-card :issue="$issue" />', ['issue' => $issue]);

    expect($html)->toContain('Card issue')
        ->toContain($issue->owner->name);
});
