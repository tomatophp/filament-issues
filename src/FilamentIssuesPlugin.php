<?php

namespace TomatoPHP\FilamentIssues;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource;

class FilamentIssuesPlugin implements Plugin
{

    public function getId(): string
    {
        return 'filament-issues';
    }


    public function register(Panel $panel): void
    {
        $panel->resources([
            IssueResource::class
        ]);
    }

    public function boot(Panel $panel): void
    {
       //
    }

    public static function make(): static
    {
        return new static();
    }
}
