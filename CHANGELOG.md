# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Version 0.2.1] — 2021-04-15

* Mark tests that use `defineFunction()` or `deleteFunction()` as skipped if Runkit is unavailable ([#25])


## [Version 0.2.0] — 2020-11-23

* Introduce a [new `AssertWell\PHPUnitGlobalState\Functions` trait](docs/Functions.md) ([#17])
* Introduce an `AssertWell\PHPUnitGlobalState\Support\Runkit` support class ([#15])
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
[#15]: https://github.com/assertwell/phpunit-global-state/pull/15
[#16]: https://github.com/assertwell/phpunit-global-state/pull/16
[#17]: https://github.com/assertwell/phpunit-global-state/pull/17
[#25]: https://github.com/assertwell/phpunit-global-state/pull/25
