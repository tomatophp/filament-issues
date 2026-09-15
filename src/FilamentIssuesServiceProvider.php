<?php

namespace TomatoPHP\FilamentIssues;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentIssues\Console\FilamentIssuesInstall;
use TomatoPHP\FilamentIssues\Console\FilamentIssuesRefresh;
use TomatoPHP\FilamentIssues\Services\FilamentIssuesServices;
use TomatoPHP\FilamentIssues\Views\Components\IssueCard;
use TomatoPHP\FilamentIssues\Views\Components\Issues;

class FilamentIssuesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            FilamentIssuesInstall::class,
            FilamentIssuesRefresh::class,
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/filament-issues.php', 'filament-issues');

        $this->publishes([
            __DIR__ . '/../config/filament-issues.php' => config_path('filament-issues.php'),
        ], 'filament-issues-config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'filament-issues-migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-issues');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-issues'),
        ], 'filament-issues-views');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-issues');

        $this->publishes([
            __DIR__ . '/../resources/lang' => base_path('lang/vendor/filament-issues'),
        ], 'filament-issues-lang');

        // A singleton so repos registered from a service provider survive until the refresh runs.
        $this->app->singleton('filament-issues', fn (): FilamentIssuesServices => new FilamentIssuesServices);

        $this->loadViewComponentsAs('filament', [
            IssueCard::class,
            Issues::class,
        ]);
    }

    public function boot(): void
    {
        //
    }
}
