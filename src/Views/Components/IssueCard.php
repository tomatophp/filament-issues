<?php

namespace TomatoPHP\FilamentIssues\Views\Components;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\View\Component;
use TomatoPHP\FilamentIssues\Models\Issue;

class IssueCard extends Component
{
    public function __construct(
        public Issue $issue,
        public bool $isIgnored = false
    ) {}

    public function render(): Closure | Htmlable | Factory | View | Application | \Illuminate\View\View | string
    {
        return view('filament-issues::components.issue-card');
    }
}
