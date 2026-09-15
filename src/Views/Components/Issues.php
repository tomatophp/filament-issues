<?php

namespace TomatoPHP\FilamentIssues\Views\Components;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\View\Component;

class Issues extends Component
{
    public function __construct() {}

    public function render(): Closure | Htmlable | Factory | View | Application | \Illuminate\View\View | string
    {
        return view('filament-issues::components.issues');
    }
}
