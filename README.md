# vaibhavpandeyvpz/phlash
Package for flashing data now or later (in next request) for use with any micro/full-stack framework.

[![Build status][build-status-image]][build-status-url]
[![Code Coverage][code-coverage-image]][code-coverage-url]
[![Latest Version][latest-version-image]][latest-version-url]
[![Downloads][downloads-image]][downloads-url]
[![PHP Version][php-version-image]][php-version-url]
[![License][license-image]](LICENSE)

Install
-------
```bash
composer require vaibhavpandeyvpz/phlash
```

Usage
-----

```php
<?php

$flash = new Phlash\ArrayFlash();

/**
 * @desc These will be available in current request.
 */
$flash->flashNow('messages', [
    'Thank your for registering with us.' => 'success',
]);

/**
 * @desc These will be available in next request.
 */
$flash->flashLater('errors', [
    'name' => ['This field is required.'],
]);

/**
 * @desc Get all (or by key) flashed data for the current + previous request.
 */
$messages = $flash->get() || $flash->get('messages');
```

License
-------
See [LICENSE](LICENSE) file.

[build-status-image]: https://img.shields.io/travis/vaibhavpandeyvpz/phlash.svg?style=flat-square
[build-status-url]: https://travis-ci.org/vaibhavpandeyvpz/phlash
[code-coverage-image]: https://img.shields.io/codecov/c/github/vaibhavpandeyvpz/phlash.svg?style=flat-square
[code-coverage-url]: https://codecov.io/gh/vaibhavpandeyvpz/phlash
[latest-version-image]: https://img.shields.io/github/release/vaibhavpandeyvpz/phlash.svg?style=flat-square
[latest-version-url]: https://github.com/vaibhavpandeyvpz/phlash/releases
[downloads-image]: https://img.shields.io/packagist/dt/vaibhavpandeyvpz/phlash.svg?style=flat-square
[downloads-url]: https://packagist.org/packages/vaibhavpandeyvpz/phlash
[php-version-image]: http://img.shields.io/badge/php-5.3+-8892be.svg?style=flat-square
[php-version-url]: https://packagist.org/packages/vaibhavpandeyvpz/phlash
[license-image]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
