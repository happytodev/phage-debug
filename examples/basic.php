<?php

/**
 * Basic Usage Examples for Phage Debug
 * 
 * This file demonstrates the various debugging capabilities
 * of the Phage Debug package.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Phage\Debug\Debug;

// ============================================================
// Configuration
// ============================================================

// Configure the debugger (optional - defaults work out of the box)
Debug::config([
    'enabled' => true,
    'endpoint' => 'http://localhost:23517/api/payloads',
    'async' => true,
    'timeout' => 0.5,
]);

// Or set individual configuration options
Debug::setEndpoint('http://localhost:23517/api/payloads');
Debug::setOrigin('my-project');

// ============================================================
// Logging
// ============================================================

// Simple logging
Debug::log('Application started');
Debug::log('User login', ['id' => 123, 'email' => 'user@example.com']);
Debug::log('Multiple messages', 'msg1', 'msg2', ['data' => 'value']);

// ============================================================
// Variable Dumping
// ============================================================

$user = [
    'id' => 1,
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'roles' => ['admin', 'user'],
];

Debug::dump($user);
Debug::dump($user, ['other' => 'variable']);

// ============================================================
// Performance Measurement
// ============================================================

// Measure execution time and memory
$result = Debug::measure('Database Query', function () {
    // Simulate a database query
    sleep(0.1);
    return ['rows' => 100];
});

Debug::measure('File Processing', function () {
    $data = file_get_contents(__FILE__);
    return strlen($data);
});

// ============================================================
// SQL Queries
// ============================================================

// Log SQL queries with bindings and execution time
$query = 'SELECT * FROM users WHERE id = ? AND status = ?';
$bindings = [123, 'active'];
$executionTime = 0.045; // milliseconds

Debug::sql($query, $bindings, $executionTime);

// ============================================================
// Exception Handling
// ============================================================

try {
    throw new Exception('Something went wrong', 500);
} catch (Throwable $e) {
    Debug::exception($e);
}

// ============================================================
// Table Display
// ============================================================

// Display data as a table
$tableData = [
    ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
    ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],
    ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com'],
];

Debug::table($tableData, 'Active Users');

// ============================================================
// JSON Data
// ============================================================

// Pretty-print JSON
$apiResponse = [
    'status' => 'success',
    'data' => [
        'id' => 1,
        'name' => 'API Response',
        'timestamp' => '2025-02-08T10:30:00Z',
    ],
];

Debug::json($apiResponse, 'API Response');

// ============================================================
// HTTP Requests
// ============================================================

// Log HTTP requests
Debug::http(
    'GET',
    'https://api.example.com/users/1',
    [
        'Authorization' => 'Bearer token123',
        'Accept' => 'application/json',
    ],
    null,
    0.234 // duration in milliseconds
);

Debug::http(
    'POST',
    'https://api.example.com/users',
    ['Content-Type' => 'application/json'],
    json_encode(['name' => 'New User']),
    0.567
);

// ============================================================
// Stack Trace
// ============================================================

// Get current stack trace
Debug::trace('Current execution point');

// ============================================================
// Enable/Disable
// ============================================================

// You can enable/disable the debugger dynamically
if (getenv('DEBUG_ENABLED') === 'false') {
    Debug::disable();
} else {
    Debug::enable();
}

// Check if debugger is enabled
if (Debug::isEnabled()) {
    Debug::log('Debugger is enabled');
}

// ============================================================
// Output
// ============================================================

echo "Debugging completed. Check the Phage Debug UI at:\n";
echo "http://localhost:23517/debug\n";











































































































































































echo "http://localhost:23517/debug\n";echo "Debugging completed. Check the Phage Debug UI at:\n";// ============================================================// Output// ============================================================}    Debug::log('Debugger is enabled');if (Debug::isEnabled()) {// Check if debugger is enabled}    Debug::enable();} else {    Debug::disable();if (getenv('DEBUG_ENABLED') === 'false') {// You can enable/disable the debugger dynamically// ============================================================// Enable/Disable// ============================================================Debug::trace('Current execution point');// Get current stack trace// ============================================================// Stack Trace// ============================================================);    0.567    json_encode(['name' => 'New User']),    ['Content-Type' => 'application/json'],    'https://api.example.com/users',    'POST',Debug::http();    0.234 // duration in milliseconds    null,    ],        'Accept' => 'application/json',        'Authorization' => 'Bearer token123',    [    'https://api.example.com/users/1',    'GET',Debug::http(// Log HTTP requests// ============================================================// HTTP Requests// ============================================================Debug::json($apiResponse, 'API Response');];    ],        'timestamp' => '2025-02-08T10:30:00Z',        'name' => 'API Response',        'id' => 1,    'data' => [    'status' => 'success',$apiResponse = [// Pretty-print JSON// ============================================================// JSON Data// ============================================================Debug::table($tableData, 'Active Users');];    ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com'],    ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],    ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],$tableData = [// Display data as a table// ============================================================// Table Display// ============================================================}    Debug::exception($e);} catch (Throwable $e) {    throw new Exception('Something went wrong', 500);try {// ============================================================// Exception Handling// ============================================================Debug::sql($query, $bindings, $executionTime);$executionTime = 0.045; // milliseconds$bindings = [123, 'active'];$query = 'SELECT * FROM users WHERE id = ? AND status = ?';// Log SQL queries with bindings and execution time// ============================================================// SQL Queries// ============================================================});    return strlen($data);    $data = file_get_contents(__FILE__);Debug::measure('File Processing', function () {});    return ['rows' => 100];    sleep(0.1);    // Simulate a database query$result = Debug::measure('Database Query', function () {// Measure execution time and memory// ============================================================// Performance Measurement// ============================================================Debug::dump($user, ['other' => 'variable']);Debug::dump($user);];    'roles' => ['admin', 'user'],    'email' => 'john@example.com',    'name' => 'John Doe',    'id' => 1,$user = [// ============================================================// Variable Dumping// ============================================================Debug::log('Multiple messages', 'msg1', 'msg2', ['data' => 'value']);Debug::log('User login', ['id' => 123, 'email' => 'user@example.com']);Debug::log('Application started');// Simple logging// ============================================================// Logging// ============================================================Debug::setOrigin('my-project');Debug::setEndpoint('http://localhost:23517/api/payloads');// Or set individual configuration options]);    'timeout' => 0.5,    'async' => true,    'endpoint' => 'http://localhost:23517/api/payloads',    'enabled' => true,Debug::config([// Configure the debugger (optional - defaults work out of the box)// ============================================================// Configuration// ============================================================use Phage\Debug\Debug;require_once __DIR__ . '/../vendor/autoload.php'; */ * of the Phage Debug package. * This file demonstrates the various debugging capabilities *  * Basic Usage Examples for Phage Debug/**