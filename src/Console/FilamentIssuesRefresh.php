<?php

namespace TomatoPHP\FilamentIssues\Console;

use Illuminate\Console\Command;
use TomatoPHP\ConsoleHelpers\Traits\RunCommand;
use TomatoPHP\FilamentIssues\Facades\FilamentIssues;

class FilamentIssuesRefresh extends Command
{
    use RunCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'filament-issues:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh issues from github';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        FilamentIssues::refresh();
    }
}
