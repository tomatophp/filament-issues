<?php

namespace TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
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
                ->label(trans('filament-issues::messages.actions.refresh.label'))
                ->requiresConfirmation()
                ->icon('heroicon-o-arrow-down-circle')
                ->action(function (){
                    FilamentIssues::refresh();

                    Notification::make()
                        ->title(trans('filament-issues::messages.actions.refresh.title'))
                        ->body(trans('filament-issues::messages.actions.refresh.body'))
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
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
