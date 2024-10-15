<?php

namespace TomatoPHP\FilamentIssues\Filament\Resources;

use Illuminate\Support\HtmlString;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\Pages;
use TomatoPHP\FilamentIssues\Filament\Resources\IssueResource\RelationManagers;
use TomatoPHP\FilamentIssues\Models\Issue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static ?string $navigationIcon = 'bx-bullseye';

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

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(trans('filament-issues::messages.columns.title'))
                    ->url(fn($record) => $record->url, true)
                    ->icon(fn($record) => $record->isPullRequest ? 'bx-git-pull-request' : 'bx-bullseye')
                    ->iconColor(fn($record) => $record->isPullRequest ? 'info' : 'warning')
                    ->description(fn($record) => "#" . $record->number . " ". trans('filament-issues::messages.columns.opened'). " " .  $record->createdAt->diffForHumans() . " ".trans('filament-issues::messages.columns.in-repository')." " . $record->repoName)
                    ->searchable(),
                Tables\Columns\ImageColumn::make('owner.profilePictureUrl')
                    ->label(trans('filament-issues::messages.columns.by'))
                    ->circular()
                    ->tooltip(fn($record) => $record->owner?->name)
                    ->url(fn($record) => $record->owner->url, true),
                Tables\Columns\TextColumn::make('labels.name')
                    ->label(trans('filament-issues::messages.columns.labels'))
                    ->color('warning')
                    ->toggleable()
                    ->badge(),
                Tables\Columns\TextColumn::make('commentCount')
                    ->label(trans('filament-issues::messages.columns.comments'))
                    ->icon('bxs-comment')
                    ->iconColor('primary')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_public')
                    ->label(trans('filament-issues::messages.columns.is_public'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_trend')
                    ->label(trans('filament-issues::messages.columns.is_trend'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('createdAt', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('labels')
                    ->label(trans('filament-issues::messages.columns.labels'))
                    ->preload()
                    ->searchable()
                    ->relationship('labels', 'name'),
                Tables\Filters\SelectFilter::make('repo')
                    ->label(trans('filament-issues::messages.columns.repo'))
                    ->preload()
                    ->searchable()
                    ->relationship('repo', 'name'),
                Tables\Filters\SelectFilter::make('owner')
                    ->label(trans('filament-issues::messages.columns.by'))
                    ->preload()
                    ->searchable()
                    ->relationship('owner', 'name'),
                Tables\Filters\TernaryFilter::make('isPullRequest')
                    ->label(trans('filament-issues::messages.columns.isPullRequest'))
                ,

            ])
            ->actions([

            ])
            ->bulkActions([

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
            'index' => Pages\ListIssues::route('/'),
        ];
    }
}
