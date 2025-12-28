# Phlash

[![Latest Version](https://img.shields.io/packagist/v/vaibhavpandeyvpz/phlash.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/phlash)
[![Downloads](https://img.shields.io/packagist/dt/vaibhavpandeyvpz/phlash.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/phlash)
[![PHP Version](https://img.shields.io/packagist/php-v/vaibhavpandeyvpz/phlash.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/phlash)
[![License](https://img.shields.io/packagist/l/vaibhavpandeyvpz/phlash.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/vaibhavpandeyvpz/phlash/tests.yml?branch=master&style=flat-square)](https://github.com/vaibhavpandeyvpz/phlash/actions)

A lightweight PHP library for managing flash messages that can be made available either immediately (in the current request) or in the next request. Perfect for use with any micro or full-stack framework.

## Features

- **Simple API**: Easy-to-use interface for flashing messages
- **Two Flash Types**: Flash messages for current request or next request
- **Framework Agnostic**: Works with any PHP framework or vanilla PHP
- **Type Safe**: Built with PHP 8.2+ features including strict types, enums, and type hints
- **Flexible Storage**: Uses `$_SESSION` by default, but accepts custom storage arrays
- **Zero Dependencies**: No external dependencies required
- **Fully Tested**: 100% code coverage with comprehensive test suite

## Requirements

- PHP 8.2 or higher

## Installation

Install via Composer:

```bash
composer require vaibhavpandeyvpz/phlash
```

## Quick Start

```php
<?php

use Phlash\ArrayFlash;

// Create a flash instance (uses $_SESSION by default)
$flash = new ArrayFlash();

// Flash a message for the current request
$flash->flashNow('success', 'Your changes have been saved!');

// Flash a message for the next request
$flash->flashLater('error', 'Please correct the errors below.');

// Retrieve messages
$success = $flash->get('success');  // Available immediately
$error = $flash->get('error');      // null (not available until next request)
```

## Usage

### Basic Usage

#### Flash Messages for Current Request

Messages flashed with `flashNow()` are immediately available in the same request:

```php
<?php

use Phlash\ArrayFlash;

$flash = new ArrayFlash();

// Flash a success message
$flash->flashNow('success', 'Operation completed successfully!');

// Retrieve it immediately
$message = $flash->get('success');
echo $message; // "Operation completed successfully!"
```

#### Flash Messages for Next Request

Messages flashed with `flashLater()` are not available in the current request, but will be available when a new instance is created (simulating the next request):

```php
<?php

use Phlash\ArrayFlash;

// In your controller/action (Request 1)
$flash = new ArrayFlash();
$flash->flashLater('message', 'You have been redirected successfully!');

// The message is NOT available yet
$msg = $flash->get('message'); // null

// In the next request (Request 2)
$flash = new ArrayFlash();
$msg = $flash->get('message'); // "You have been redirected successfully!"
```

### Retrieving Messages

#### Get All Messages

```php
<?php

$flash = new ArrayFlash();
$flash->flashNow('error', 'Invalid input');
$flash->flashNow('warning', 'Please review your data');

$all = $flash->get(); // ['error' => 'Invalid input', 'warning' => 'Please review your data']
```

#### Get Specific Message

```php
<?php

$flash = new ArrayFlash();
$flash->flashNow('message', 'Hello World');

$message = $flash->get('message'); // 'Hello World'
$missing = $flash->get('nonexistent'); // null
```

### Custom Storage

By default, `ArrayFlash` uses `$_SESSION` for storage. You can provide a custom array for storage:

```php
<?php

use Phlash\ArrayFlash;

// Use a custom storage array
$customStorage = [];
$flash = new ArrayFlash($customStorage);

$flash->flashNow('test', 'value');

// The storage array is modified by reference
var_dump($customStorage);
// ['Phlash' => ['now' => ['test' => 'value'], 'later' => []]]
```

### Common Use Cases

#### Form Validation Errors

```php
<?php

use Phlash\ArrayFlash;

$flash = new ArrayFlash();

// After form submission with errors
$flash->flashLater('errors', [
    'email' => ['The email field is required.'],
    'password' => ['The password must be at least 8 characters.'],
]);

// Redirect to form page
header('Location: /form');
exit;
```

```php
<?php
// On the form page (next request)
use Phlash\ArrayFlash;

$flash = new ArrayFlash();
$errors = $flash->get('errors'); // ['email' => [...], 'password' => [...]]

if ($errors) {
    foreach ($errors as $field => $messages) {
        foreach ($messages as $message) {
            echo "<div class='error'>$message</div>";
        }
    }
}
```

#### Success Messages

```php
<?php

use Phlash\ArrayFlash;

$flash = new ArrayFlash();

// After successful operation
$flash->flashLater('success', 'Your profile has been updated successfully!');

// Redirect
header('Location: /profile');
exit;
```

```php
<?php
// On the profile page (next request)
use Phlash\ArrayFlash;

$flash = new ArrayFlash();
$success = $flash->get('success');

if ($success) {
    echo "<div class='success'>$success</div>";
}
```

#### Mixed Messages

```php
<?php

use Phlash\ArrayFlash;

$flash = new ArrayFlash();

// Flash multiple types of messages
$flash->flashNow('info', 'Processing your request...');
$flash->flashLater('success', 'Operation completed!');
$flash->flashLater('notifications', [
    'New message received',
    'Friend request accepted',
]);

// Get all current messages
$current = $flash->get(); // ['info' => 'Processing your request...']

// In next request, get all messages
$flash = new ArrayFlash();
$all = $flash->get();
// ['success' => 'Operation completed!', 'notifications' => [...]]
```

### Data Types

Phlash supports any data type for flash messages:

```php
<?php

use Phlash\ArrayFlash;

$flash = new ArrayFlash();

// Strings
$flash->flashNow('message', 'Hello World');

// Arrays
$flash->flashNow('data', ['key' => 'value', 'count' => 42]);

// Objects (will be serialized if using $_SESSION)
$flash->flashNow('user', new stdClass());

// Integers, floats, booleans
$flash->flashNow('count', 100);
$flash->flashNow('price', 29.99);
$flash->flashNow('active', true);

// Complex nested structures
$flash->flashNow('complex', [
    'user' => [
        'name' => 'John Doe',
        'preferences' => [
            'theme' => 'dark',
            'notifications' => true,
        ],
    ],
]);
```

## API Reference

### `ArrayFlash`

The main implementation class for flash message storage.

#### Constructor

```php
public function __construct(?array &$storage = null)
```

- **Parameters:**
    - `$storage` (array|null): Optional array reference for storage. If `null`, uses `$_SESSION`.
- **Behavior:** On construction, messages from the "later" bag (previous request) are moved to "now" bag, and "later" is cleared.

#### Methods

##### `flashNow(string $key, mixed $message): void`

Flash a message to be available in the current request.

- **Parameters:**
    - `$key` (string): The key to store the message under
    - `$message` (mixed): The message data (any type)
- **Returns:** `void`

##### `flashLater(string $key, mixed $message): void`

Flash a message to be available in the next request.

- **Parameters:**
    - `$key` (string): The key to store the message under
    - `$message` (mixed): The message data (any type)
- **Returns:** `void`

##### `get(?string $key = null): mixed`

Retrieve flashed messages.

- **Parameters:**
    - `$key` (string|null): Optional key to retrieve a specific message. If `null`, returns all messages.
- **Returns:**
    - `array<string, mixed>` when `$key` is `null` (all messages)
    - `mixed` when `$key` is provided and exists (the message value)
    - `null` when `$key` is provided but doesn't exist

## Architecture

Phlash uses a simple but effective architecture:

- **FlashInterface**: Defines the contract for flash implementations
- **FlashAbstract**: Base class providing common functionality
- **ArrayFlash**: Concrete implementation using array storage
- **FlashBag**: Enum defining the two flash types (NOW and LATER)

The library uses a "bag" system where messages are stored in either the "now" bag (current request) or "later" bag (next request). When a new `ArrayFlash` instance is created, messages from "later" are automatically moved to "now", simulating the transition between requests.

## Testing

The library includes a comprehensive test suite with 100% code coverage:

```bash
composer test
```

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Author

**Vaibhav Pandey**

- Email: contact@vaibhavpandey.com
- GitHub: [@vaibhavpandeyvpz](https://github.com/vaibhavpandeyvpz)
