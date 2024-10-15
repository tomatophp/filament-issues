<?php

namespace TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ManageRecords;
use TomatoPHP\FilamentIssues\Facades\FilamentIssues;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource;
use TomatoPHP\FilamentIssues\Models\Issue;

class ListIssues extends ManageRecords
{
    protected static string $resource = IssueResource::class;

    public array $ignoredUrls = [];

    public bool $showIgnoredIssues = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->requiresConfirmation()
                ->icon('heroicon-o-arrow-down-circle')
                ->label('Refresh')
                ->action(function (){
                    FilamentIssues::refresh();
                }),
        ];
    }

    public function updateIgnoredUrls(array $urls): void
    {
        $this->ignoredUrls = collect($urls)
            ->filter(function (string $url): bool {
                return $this->getTableRecords()->where('url', $url)->isNotEmpty();
            })
            ->toArray();

        if (! $this->ignoredUrls) {
            $this->showIgnoredIssues = false;
        }
    }
}
