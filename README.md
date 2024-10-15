![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-issues/master/arts/3x1io-tomato-issues.jpg)

# Filament GitHub Issues Manager

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-issues/version.svg)](https://packagist.org/packages/tomatophp/filament-issues)
[![License](https://poser.pugx.org/tomatophp/filament-issues/license.svg)](https://packagist.org/packages/tomatophp/filament-issues)
[![Downloads](https://poser.pugx.org/tomatophp/filament-issues/d/total.svg)](https://packagist.org/packages/tomatophp/filament-issues)

Manage your GitHub issues from your FilamentPHP panel and share issues with others

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
