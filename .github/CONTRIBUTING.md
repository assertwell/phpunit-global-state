# Contributing to PHPUnit Global State

Thank you for your interest in contributing to [the assertwell/phpunit-global-state](https://github.com/assertwell/phpunit-global-state) library.


## Code of Conduct

Please note that this project is released with [a Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.


## Installing a development version

This project uses [Composer](https://getcomposer.org) to manage dependencies. You may install everything you need to get started by cloning the repository and running:

```sh
$ composer install
```

If you plan to work with any of the Runkit-based traits, you'll also need to ensure that Runkit7 is installed in your environment. This can be accomplished easily via [the stevegrunwell/runkit7-installer package](https://github.com/stevegrunwell/runkit7-installer), which is provided as a development dependency to this project:

```sh
$ sudo vendor/bin/install-runkit.sh
```


### Running tests

Once the project's dependencies have been installed, you may execute the library's test suites by running any of the following:

```sh
# Run all unit tests and check coding standards
$ composer test

# Run only the PHPUnit test suite(s)
$ composer test:unit

# Check coding standards
$ composer test:standards
```

It is expected that every pull request will include relevant unit tests.


### Generating code coverage

Code coverage may be generated for the project by running the following:

```sh
$ composer test:coverage
```

This will generate HTML code coverage reports in the `tests/coverage` directory.


## Coding Standards

The project strives to adhere to [the PSR-12 coding standards](https://www.php-fig.org/psr/psr-12/), and includes an `.editorconfig` file to help with enforcement.

It's recommended that you [install an EditorConfig plugin for your editor of choice](https://editorconfig.org/) if you plan to contribute code.


### PHP Compatibility

Given that WordPress is one of the main reasons for creating this library, PHP compatibility requirements should match that of the platform (currently PHP 5.6 or newer).

Once PHP 5.6 is dropped as a requirement for WordPress, [this library will aim to quickly add typehints supported in PHP 7.x](https://github.com/assertwell/phpunit-global-state/issues/8) and other PHP 7-specific niceties.


## Branching Model

This project uses [the "GitFlow" branching strategy](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow):

* `develop` represents the latest development code, and serves as the basis for all branches.
* `master` represents the latest stable release.


### Example

Let's say you want to add a new `StateOfTheWorld` trait to the project.

First, you would create a new branch to house your work, using `develop` as a base:

```sh
$ git checkout -b feature/state-of-the-world develop
```

Then, in your branch, create the trait in `src/StateOfTheWorld.php` and corresponding test class in `tests/StateOfTheWorldTest.php`, using the existing files as templates.

When you're satisfied with the new trait, commit your changes and open a new pull request against the `develop` branch of the upstream repository.

### Tagging releases

When a new release is ready to be tagged, a `release/vX.X.X` branch will be created using `develop` as its base, then the `CHANGELOG.md` file should be updated with all significant changes in the release.

The release branch should then be pushed to GitHub and a pull request opened against the `master` branch for review.

Once merged, a new release should be created based on the `master` branch, using the format of `vX.X.X` (e.g. "v1.2.3") and containing the contents of the changelog for that release.

Finally, the `master` branch should be merged into `develop`, ensuring the code from the release branch is represented there.
