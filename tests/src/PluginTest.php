<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Artisan;
use TomatoPHP\FilamentIssues\Facades\FilamentIssues;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource;
use TomatoPHP\FilamentIssues\FilamentIssuesPlugin;

it('registers the plugin and the issues resource on the panel', function () {
    $panel = Filament::getPanel('admin');

    expect($panel->getPlugin('filament-issues'))->toBeInstanceOf(FilamentIssuesPlugin::class)
        ->and($panel->getResources())->toContain(IssueResource::class);
});

it('registers the install and refresh commands', function () {
    expect(Artisan::all())->toHaveKeys(['filament-issues:install', 'filament-issues:refresh']);
});

it('keeps repos registered from a service provider until the refresh runs', function () {
    FilamentIssues::register(['tomatophp/filament-issues', 'tomatophp/filament-cms']);

    expect(app('filament-issues')->getRepos())->toBe(['tomatophp' => ['filament-issues', 'filament-cms']]);
});
