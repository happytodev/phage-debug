# Phage Debug

Built-in debugging system for PHP development with Phage. Simple and elegant debugging tool with zero configuration.

## Features

- 📝 **Logging** - Log messages with context
- 🔍 **Collapsible Variables** - Inspect variables with expandable tree view
- 🎨 **Syntax Coloring** - Color-coded display by type
- 📦 **Object Inspection** - View properties (public/protected/private)
- ⏱️ **Performance Measurement** - Measure execution time and memory
- 🗄️ **SQL Logging** - Log database queries with bindings
- ❌ **Exception Handling** - Capture and analyze exceptions
- 📊 **Table Display** - Display data as formatted tables
- 📡 **HTTP Requests** - Log HTTP requests and responses
- 🔄 **Stack Traces** - Get detailed stack traces
- ✨ **Zero Configuration** - Works out of the box
- 📡 **Real-time Updates** - Live WebSocket connection to debug UI
- 💾 **Persistent History** - SQLite database backend

## Installation

Install via Composer:

```bash
composer require happytodev/phage-debug
```

## Requirements

- PHP 7.4 or higher
- Phage debug server running on `http://localhost:23517` (default)

## Quick Start

**1. Make sure the Phage debug server is running:**

```bash
./phage debug:start
```

**2. Use in your PHP code:**

```php
<?php

use Phage\Debug\Debug;

// Simple logging
Debug::log('Application started');
Debug::log('User data', $user);

// Dump variables with interactive tree view
Debug::dump($_GET, $_POST, $complexArray);

// Log queries
Debug::sql('SELECT * FROM users', [], 0.045);

// Log exceptions
try {
    // ...
} catch (Exception $e) {
    Debug::exception($e);
}

// Measure performance
Debug::measure('Database Query', function() {
    return User::all();
});

// Display as table
Debug::table($users, 'Active Users');

// Pretty-print JSON
Debug::json(['status' => 'success', 'data' => $data]);
```

**3. Open the debug UI:**

```bash
./phage debug:open
```

Or manually open: `http://localhost:23517`

## UI Features

The debug UI displays:

**Collapsible Arrays:**
```
▶ Array(5)
  [0]: "item1"
  [1]: "item2"
  [id]: 123
```

**Collapsible Objects:**
```
▶ User(4 properties)
  $id (private): 123
  $name (private): "John Doe"
  $email (private): "john@example.com"
  $roles (protected): Array(2)
    [0]: "admin"
    [1]: "user"
```

**Primitive Types:**
```
string: "Hello World"
integer: 42
float: 3.14
boolean: true
null: null
```

## Configuration

Configure globally in your bootstrap code:

```php
use Phage\Debug\Debug;

Debug::config([
    'enabled' => true,
    'endpoint' => 'http://localhost:23517/api/payloads',
    'async' => true,           // Non-blocking requests
    'timeout' => 0.5,          // 500ms timeout
]);
```

Or configure individually:

```php
Debug::setEndpoint('http://192.168.1.100:23517/api/payloads');
Debug::setOrigin('my-project');
```

## API Reference

### Logging & Dumping

- `Debug::log(...$messages)` - Log messages with optional context
- `Debug::dump(...$vars)` - Dump variables with full tree view

### Measurement

- `Debug::measure($label, $callback)` - Measure execution time and memory
- `Debug::sql($sql, $bindings, $time)` - Log SQL queries with bindings
- `Debug::exception($throwable)` - Log exceptions with stack trace

### Data Display

- `Debug::table($data, $label)` - Display data as formatted table
- `Debug::json($data, $label)` - Pretty-print JSON
- `Debug::http($method, $url, $headers, $body, $duration)` - Log HTTP requests

### Utilities

- `Debug::trace($label)` - Get current stack trace
- `Debug::enable()` - Enable debugging
- `Debug::disable()` - Disable debugging
- `Debug::isEnabled()` - Check if debugging is enabled

## Advanced Usage

### Disable for Production

```php
if (env('APP_DEBUG')) {
    Debug::enable();
} else {
    Debug::disable();
}
```

### Custom Origin

```php
// Defaults to composer.json "name" field
Debug::setOrigin('my-custom-project-name');
```

### Remote Debugging

Connect to a remote Phage debug server:

```php
Debug::setEndpoint('http://192.168.1.100:23517/api/payloads');
Debug::log('Log from remote client');
```

### Synchronous Mode

By default, debug requests are non-blocking (async). Force synchronous mode:

```php
Debug::config([
    'async' => false,  // Wait for responses
    'timeout' => 2.0,  // 2 second timeout
]);
```

## How It Works

1. Your PHP code calls `Debug::log()`, `Debug::dump()`, etc.
2. The package serializes the data (arrays, objects, primitives) into a detailed structure
3. A non-blocking HTTP POST request sends the data to the Phage debug server
4. The server broadcasts the payload to connected WebSocket clients (the UI)
5. The UI renders the data with interactive collapsible trees
6. Data is also persisted in SQLite for history

## Supported Types

- **Primitives:** null, bool, int, float, string
- **Collections:** array (with unlimited nesting)
- **Objects:** Any PHP object (with property inspection)
- **Special:** DateTime objects with formatted display

## Recursion Limit

To prevent infinite loops with circular references, the package limits recursive depth to **10 levels**. Deeper nesting is truncated with a `[Max depth reached]` indicator.

## License

MIT License - see LICENSE file for details

## Support

For issues, questions, or contributions:

- **Repository:** https://github.com/happytodev/phage-debug
- **Packagist:** https://packagist.org/packages/happytodev/phage-debug
- **Issues:** https://github.com/happytodev/phage-debug/issues

## Author

[HappyToDev](https://github.com/happytodev)
