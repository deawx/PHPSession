# PHPSession

A library for securely holding and managing PHP sessions in segments.

[![Latest Stable Version](http://poser.pugx.org/muhametsafak/php-session/v)](https://packagist.org/packages/muhametsafak/php-session) [![Total Downloads](http://poser.pugx.org/muhametsafak/php-session/downloads)](https://packagist.org/packages/muhametsafak/php-session) [![Latest Unstable Version](http://poser.pugx.org/muhametsafak/php-session/v/unstable)](https://packagist.org/packages/muhametsafak/php-session) [![License](http://poser.pugx.org/muhametsafak/php-session/license)](https://packagist.org/packages/muhametsafak/php-session) [![PHP Version Require](http://poser.pugx.org/muhametsafak/php-session/require/php)](https://packagist.org/packages/muhametsafak/php-session)


## Requirements

- PHP 7.4 or higher
- PHP OPENSSL Extension
- PHP JSON Extenstion
- [PHPParameterBag Library](https://github.com/muhametsafak/PHPParameterBag)

## Installation

```
composer require muhametsafak/php-session
```

## Usage

```php 
require_once "vendor/autoload.php";
use \PHPSession\Segment;

$options = [];
$user = new Segment('user', Segment::DRIVER_SESSION, $options);

# This session is destroyed after 120 seconds.
$user->set('name', 'muhametsafak', 120);

# This data will be stored until the session is terminated.
$user->set('id', 123);

# Returns session data.
$user->get('id');

# Destroy session data.
$user->destroy();
```

### Configuration

The array to be defined if `\PHPSession\Segment::DRIVER_COOKIE` is used instead of `\PHPSession\Segment::DRIVER_SESSION`.

```php 
$options = [
    'domain'    => null,    // Cookie Domain info
    'path'      => '/',     // Cookie Path info
    'secure'    => false,   // Cookie Secure info
    'httponly'  => true,    // Cookie HTTPOnly info
    'samesite'  => 'Strict', // Cookie SameSite info
    'signalgo'  => 'sha256', // Signing algorithm
    'algo'      => 'aes-128-cbc', // Encryption algorithm
    'key'       => null // Top secret key for encryption and decryption.
];
```

### Methods

#### `version()`

```php 
public function version(): string;
```

#### `destroy()`

```php 
public function destroy(): void;
```

#### `get()`

```php 
public function get(string $key, mixed $default = null): mixed;
```

#### `set()`

```php 
public function set(string $key, mixed $value, null|int $ttl = null): \PHPSession\Interfaces\SegmentInterface;
```

#### `has()`

```php 
public function has(string $key): bool;
```

#### `remove()`

```php 
public function remove(string $key): \PHPSession\Interfaces\SegmentInterface;
```

#### `all()`

```php 
public function all(): array;
```

## License

Copyright &copy; 2022 [Muhammet ŞAFAK](https://www.muhammetsafak.com.tr)

- [MIT License](https://github.com/muhametsafak/PHPSession/blob/main/LICENSE)
