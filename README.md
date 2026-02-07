# Phage Debug

Built-in debugging system for PHP development with Phage. Simple and elegant debugging tool with zero configuration.

## Features

- 📝 **Logging** - Log messages with context
- 🔍 **Variable Dumping** - Inspect variables easily
- ⏱️ **Performance Measurement** - Measure execution time and memory
- 🗄️ **SQL Logging** - Log database queries with bindings
- ❌ **Exception Handling** - Capture and analyze exceptions
- 📊 **Table Display** - Display data as formatted tables
- 📡 **HTTP Requests** - Log HTTP requests and responses
- 🔄 **Stack Traces** - Get detailed stack traces
- ✨ **Zero Configuration** - Works out of the box

## Installation

Install via Composer:

```bash
composer require happytodev/phage-debug
```

## Quick Start

```php
<?php

use Phage\Debug\Debug;

// Enable debugging
Debug::log('Application started');
Debug::dump($user);
Debug::sql($query, $bindings, $executionTime);
Debug::exception($e);
Debug::measure('API Call', fn() => $result);
```

## Configuration

```php
Debug::config([
    'enabled' => true,
    'endpoint' => 'http://localhost:23517/api/payloads',
    'async' => true,
    'timeout' => 0.5,
]);
```

## API Reference

### Logging & Dumping

- `Debug::log(...$messages)` - Log messages
- `Debug::dump(...$vars)` - Dump variables

### Measurement

- `Debug::measure($label, $callback)` - Measure execution time and memory
- `Debug::sql($sql, $bindings, $time)` - Log SQL queries
- `Debug::exception($throwable)` - Log exceptions

### Data Display

- `Debug::table($data, $label)` - Display as table
- `Debug::json($data, $label)` - Pretty-print JSON
- `Debug::http($method, $url, $headers, $body, $duration)` - Log HTTP

### Utilities

- `Debug::trace($label)` - Get stack trace
- `Debug::enable()` - Enable debugging
- `Debug::disable()` - Disable debugging
- `Debug::isEnabled()` - Check if enabled

## License

MIT License - see LICENSE file for details.

## Author

[HappyToDev](https://github.com/happytodev)
