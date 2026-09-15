<?php

namespace TomatoPHP\FilamentIssues\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\Pages\ListIssues;
use TomatoPHP\FilamentIssues\Models\Issue;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static string | \BackedEnum | null $navigationIcon = 'bx-bullseye';

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return trans('filament-issues::messages.group');
    }

    public static function getNavigationLabel(): string
    {
        return trans('filament-issues::messages.title');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-issues::messages.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-issues::messages.single');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(trans('filament-issues::messages.columns.title'))
                    ->url(fn ($record) => $record->url, true)
                    ->icon(fn ($record) => $record->isPullRequest ? 'bx-git-pull-request' : 'bx-bullseye')
                    ->iconColor(fn ($record) => $record->isPullRequest ? 'info' : 'warning')
                    ->description(fn ($record) => '#' . $record->number . ' ' . trans('filament-issues::messages.columns.opened') . ' ' . $record->createdAt->diffForHumans() . ' ' . trans('filament-issues::messages.columns.in-repository') . ' ' . $record->repoName)
                    ->searchable(),
                ImageColumn::make('owner.profilePictureUrl')
                    ->label(trans('filament-issues::messages.columns.by'))
                    ->circular()
                    ->tooltip(fn ($record) => $record->owner?->name)
                    ->url(fn ($record) => $record->owner->url, true),
                TextColumn::make('labels.name')
                    ->label(trans('filament-issues::messages.columns.labels'))
                    ->color('warning')
                    ->toggleable()
                    ->badge(),
                TextColumn::make('commentCount')
                    ->label(trans('filament-issues::messages.columns.comments'))
                    ->icon('bxs-comment')
                    ->iconColor('primary')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_public')
                    ->label(trans('filament-issues::messages.columns.is_public'))
                    ->toggleable()
                    ->sortable(),
                ToggleColumn::make('is_trend')
                    ->label(trans('filament-issues::messages.columns.is_trend'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('createdAt', 'desc')
            ->filters([
                SelectFilter::make('labels')
                    ->label(trans('filament-issues::messages.columns.labels'))
                    ->preload()
                    ->searchable()
                    ->relationship('labels', 'name'),
                SelectFilter::make('repo')
                    ->label(trans('filament-issues::messages.columns.repo'))
                    ->preload()
                    ->searchable()
                    ->relationship('repo', 'name'),
                SelectFilter::make('owner')
                    ->label(trans('filament-issues::messages.columns.by'))
                    ->preload()
                    ->searchable()
                    ->relationship('owner', 'name'),
                TernaryFilter::make('isPullRequest')
                    ->label(trans('filament-issues::messages.columns.isPullRequest')),

            ])
            ->recordActions([

            ])
            ->toolbarActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIssues::route('/'),
        ];
    }
}
