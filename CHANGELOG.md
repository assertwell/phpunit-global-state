# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Version 0.2.2] – 2022-09-05

### Fixed

* Fix a PHP 8.2 deprecation warning ([#27], props @brettmc)
* Add PHP 8.x to the GitHub Actions testing matrix
* Update PHPStan to 1.8
* Clean up deprecation warnings related to PHPUnit Bridge

## [Version 0.2.1] — 2021-04-15

### Fixed

* Mark tests that use `defineFunction()` or `deleteFunction()` as skipped if Runkit is unavailable ([#25])
* Fix coding standards, remove unused namespace imports ([#22], props [@peter279k](https://github.com/peter279k))

## [Version 0.2.0] — 2020-11-23

### Added

* Introduce a [new `AssertWell\PHPUnitGlobalState\Functions` trait](docs/Functions.md) ([#17])
* Introduce an `AssertWell\PHPUnitGlobalState\Support\Runkit` support class ([#15])

### Updated

* Simplify the cleanup between tests of of the private properties that hold changes ([#16])

## [Version 0.1.0] — 2020-09-25

Initial public release of the package, with the following traits:

* [`AssertWell\PHPUnitGlobalState\Constants`](docs/Constants.md)
* [`AssertWell\PHPUnitGlobalState\EnvironmentVariables`](docs/EnvironmentVariables.md)
* [`AssertWell\PHPUnitGlobalState\GlobalVariables`](docs/GlobalVariables.md)

[Unreleased]: https://github.com/assertwell/phpunit-global-state/compare/master...develop
[Version 0.1.0]: https://github.com/assertwell/phpunit-global-state/tag/v0.1.0
[Version 0.2.0]: https://github.com/assertwell/phpunit-global-state/tag/v0.2.0
[Version 0.2.1]: https://github.com/assertwell/phpunit-global-state/tag/v0.2.1
[Version 0.2.2]: https://github.com/assertwell/phpunit-global-state/tag/v0.2.2
[#15]: https://github.com/assertwell/phpunit-global-state/pull/15
[#16]: https://github.com/assertwell/phpunit-global-state/pull/16
[#17]: https://github.com/assertwell/phpunit-global-state/pull/17
[#22]: https://github.com/assertwell/phpunit-global-state/pull/22
[#25]: https://github.com/assertwell/phpunit-global-state/pull/25
[#27]: https://github.com/assertwell/phpunit-global-state/pull/27
