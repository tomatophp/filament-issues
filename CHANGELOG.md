# Changelog

### v5.0.0

- support Filament v5 and Laravel 12 / 13 (the Filament v3 line continues on the `v3` branch)
- register the `filament-issues:refresh` command the README documents
- fix `ensureRepoCanBeCrawled()` depending on a `DataTransferObjects\Repository` class that does not exist
- fetch every configured, registered and organization repository once (it was fetched once per owner)
- merge repos from the config, the facade and `orgs` instead of overwriting them per owner
- stop crawling an organization on an error response instead of looping forever
- update reaction counts on refresh instead of attaching duplicate rows
- read the issue labels from `filament-issues.labels` (it read `repos.labels`)
- keep repos registered from a service provider (the service is now a singleton)
- ship the translations the public issues component uses instead of `cms::messages`
- fix `fromArray()` on the label, reaction and owner models
- model factories, a Pest suite with faked GitHub HTTP calls, a phpstan config and the Laravel 12 / 13 CI matrix
- new cover and screenshots
