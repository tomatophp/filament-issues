![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-issues/master/arts/3x1io-tomato-issues.jpg)

# Filament GitHub Issues Manager

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-issues/version.svg)](https://packagist.org/packages/tomatophp/filament-issues)
[![License](https://poser.pugx.org/tomatophp/filament-issues/license.svg)](https://packagist.org/packages/tomatophp/filament-issues)
[![Downloads](https://poser.pugx.org/tomatophp/filament-issues/d/total.svg)](https://packagist.org/packages/tomatophp/filament-issues)

Manage your GitHub issues from your FilamentPHP panel and share issues with others

## Features

- [x] Fetch issues from GitHub
- [x] Filter issues by labels
- [x] Filter issues by assignees
- [x] Filter issues by author
- [x] Filter issues by created date
- [x] Filter issues by repository
- [x] Issues UI Component
- [x] Issues Card Component
- [x] Refresh issues command
- [x] Register Repo from Service Provider
- [x] Integration with Filament CMS Builder
- [ ] Manage Issues from FilamentPHP panel
- [ ] Share Issues with others
- [ ] Filter By Milestones
- [ ] Filter By Projects
- [ ] Filter By Reactions
- [ ] Add comments to issues
- [ ] Add labels to issues
- [ ] Add assignees to issues
- [ ] Add milestones to issues
- [ ] Add projects to issues
- [ ] Add reactions to issues
- [ ] Integration With Jira
- [ ] Integration With Filament PMS `coming soon`

## Screenshots

![Issues Dashboard](https://raw.githubusercontent.com/tomatophp/filament-issues/master/arts/issues.png)
![Issues Filters](https://raw.githubusercontent.com/tomatophp/filament-issues/master/arts/issues-filters.png)
![Issues Component](https://raw.githubusercontent.com/tomatophp/filament-issues/master/arts/issues-component.png)

## Installation

```bash
composer require tomatophp/filament-issues
```
after install your package please run this command

```bash
php artisan filament-issues:install
```


if you are not using this package as a plugin please register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentIssues\FilamentIssuesPlugin::make())
```

now you need to publish the config file `filament-issues`

```bash
php artisan vendor:publish --tag="filament-issues-config"
```

now on your config file edit orgs and repos so you can select which organization and repository you want to fetch issues from

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Organizations
    |--------------------------------------------------------------------------
    |
    | List of organizations to search for issues.
    |
    */
    'orgs' => [],

    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    |
    | List of repositories to search for issues.
    |
    */
    'repos' => [],

]
```

now on your `services.php` config add this

```php
'github' => [
    'username' => env('GITHUB_USERNAME'),
    'token' => env('GITHUB_TOKEN'),
],
```

and on your `.env` file add this

```env
GITHUB_USERNAME=your-github-username
GITHUB_TOKEN=your-github-token
```

now clear your config

```bash
php artisan config:cache
```

after install you will find a refresh button on the issues resource you can click it the fetch your issues from GitHub make sure your queue is running

## Usage

you can use this Issues on public by just use this component

```html
<x-filament-issues />
```

or you can use direct issue card by use this component

```html
<x-filament-issues-card :issue="$issue" />
```

## Refresh Your issues 

we create a predefined command to refresh your issues by use this command

```bash
php artisan filament-issues:refresh
```

## Register Repo using Facade

you can register your repo by use this code on your `AppServiceProvider.php`

```php
public function boot() 
{
    FilamentIssues::register([
        'tomatophp/filament-issues',
        'tomatophp/filament-cms',
        'tomatophp/filament-pms',
    ]);
}
```


## Integration With Filament CMS Builder

you can use this package with [Filament CMS Builder](https://www.github.com/tomatophp/filament-cms) by use this code on your `AppServiceProvider.php`

```php
public function boot() 
{
    FilamentIssues::register(
        fn() => Post::query()
            ->where('type', 'open-source')
            ->pluck('meta_url')
            ->map(
                fn($item) => str($item)
                    ->remove('https://github.com/')
                    ->remove('https://www.github.com/')
                    ->toString()
            )
            ->toArray()
   );
} 
```

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-issues-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-issues-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-issues-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-issues-migrations"
```

## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
