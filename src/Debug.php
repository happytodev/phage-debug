<?php

namespace Phage\Debug;

/**
 * Phage Debug - Built-in debugging system for PHP development
 * 
 * Simple and elegant debugging tool with zero configuration.
 * 
 * @example 
 * use Phage\Debug\Debug;
 * 
 * Debug::log('User data', $user);
 * Debug::sql($query, $bindings, $executionTime);
 * Debug::exception($e);
 * Debug::measure('API Call', fn() => $result);
 * etc.
 */
class Debug
{
    private static ?string $endpoint = null;
    private static ?string $origin = null;
    private static array $config = [
        'enabled' => true,
        'endpoint' => 'http://localhost:23517/api/payloads',
        'async' => true,
        'timeout' => 0.5,
    ];

    /**
     * Initialize the debugger with configuration
     */
    public static function config(array $options = []): void
    {
        self::$config = array_merge(self::$config, $options);
        
        if (isset($options['endpoint'])) {
            self::$endpoint = $options['endpoint'];
        }
        
        if (isset($options['origin'])) {
            self::$origin = $options['origin'];
        }
    }

    /**
     * Set the endpoint URL
     */
    public static function setEndpoint(string $endpoint): void
    {
        self::$endpoint = $endpoint;
    }

    /**
     * Set the origin (project name)
     */
    public static function setOrigin(string $origin): void
    {
        self::$origin = $origin;
    }

    /**
     * Log a message with optional context
     */
    public static function log(...$messages): void
    {
        self::send('log', [
            'messages' => array_map(self::class . '::format', $messages),
        ]);
    }

    /**
     * Dump a variable (like var_dump but formatted with expandable structures)
     */
    public static function dump(...$vars): void
    {
        $dumps = [];
        foreach ($vars as $var) {
            $dumps[] = self::toDetailedStructure($var);
        }

        self::send('dump', [
            'dumps' => $dumps,
        ]);
    }

    /**
     * Log a database query with bindings and execution time
     */
    public static function sql(string $sql, array $bindings = [], ?float $executionTime = null): void
    {
        self::send('query', [
            'sql' => $sql,
            'bindings' => $bindings,
            'time' => $executionTime,
        ]);
    }

    /**
     * Log an exception with stack trace
     */
    public static function exception(\Throwable $e): void
    {
        self::send('exception', [
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => self::formatStackTrace($e->getTrace()),
        ]);
    }

    /**
     * Measure execution time and memory of a callback
     */
    public static function measure(string $label, callable $callback): mixed
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        try {
            $result = $callback();
        } catch (\Throwable $e) {
            self::exception($e);
            throw $e;
        }

        $duration = (microtime(true) - $startTime) * 1000; // milliseconds
        $memory = memory_get_usage(true) - $startMemory;

        self::send('measure', [
            'label' => $label,
            'duration' => round($duration, 2),
            'memory' => $memory,
            'memory_formatted' => self::formatBytes($memory),
        ]);

        return $result;
    }

    /**
     * Display data as a table
     */
    public static function table(array $data, ?string $label = null): void
    {
        self::send('table', [
            'label' => $label,
            'data' => $data,
        ]);
    }

    /**
     * Pretty-print JSON data
     */
    public static function json($data, ?string $label = null): void
    {
        self::send('json', [
            'label' => $label,
            'json' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
    }

    /**
     * Log an HTTP request
     */
    public static function http(string $method, string $url, array $headers = [], $body = null, ?float $duration = null): void
    {
        self::send('http', [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => self::format($body),
            'duration' => $duration,
        ]);
    }

    /**
     * Get stack trace
     */
    public static function trace(?string $label = null): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        // Remove the trace() call itself
        array_shift($trace);

        self::send('trace', [
            'label' => $label,
            'trace' => self::formatStackTrace($trace),
        ]);
    }

    /**
     * Format value for display (simple string format)
     */
    private static function format($value): string
    {
        if (is_object($value)) {
            if ($value instanceof \DateTime) {
                return $value->format('Y-m-d H:i:s');
            }
            return get_class($value) . '(' . count((array)$value) . ' properties)';
        }

        if (is_array($value)) {
            return 'array(' . count($value) . ')';
        }

        if (is_string($value)) {
            return strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'null';
        }

        return (string)$value;
    }

    /**
     * Recursively convert value to a detailed structure for UI display
     * Supports collapsible arrays and objects
     */
    private static function toDetailedStructure($value, int $depth = 0, int $maxDepth = 10): array
    {
        if ($depth > $maxDepth) {
            return [
                'type' => 'string',
                'value' => '[Max depth reached]',
                'isExpandable' => false,
            ];
        }

        if ($value === null) {
            return [
                'type' => 'null',
                'value' => 'null',
                'isExpandable' => false,
            ];
        }

        if (is_bool($value)) {
            return [
                'type' => 'boolean',
                'value' => $value ? 'true' : 'false',
                'isExpandable' => false,
            ];
        }

        if (is_int($value) || is_float($value)) {
            return [
                'type' => is_int($value) ? 'integer' : 'float',
                'value' => (string)$value,
                'isExpandable' => false,
            ];
        }

        if (is_string($value)) {
            $isLong = strlen($value) > 100;
            return [
                'type' => 'string',
                'value' => $isLong ? substr($value, 0, 100) . '...' : $value,
                'fullValue' => $isLong ? $value : null,
                'isExpandable' => $isLong,
            ];
        }

        if (is_array($value)) {
            $items = [];
            foreach ($value as $key => $item) {
                $items[] = [
                    'key' => (string)$key,
                    'value' => self::toDetailedStructure($item, $depth + 1, $maxDepth),
                ];
            }

            return [
                'type' => 'array',
                'count' => count($value),
                'value' => 'array(' . count($value) . ')',
                'isExpandable' => true,
                'items' => $items,
            ];
        }

        if (is_object($value)) {
            if ($value instanceof \DateTime) {
                return [
                    'type' => 'object',
                    'class' => 'DateTime',
                    'value' => $value->format('Y-m-d H:i:s'),
                    'isExpandable' => false,
                ];
            }

            $reflection = new \ReflectionClass($value);
            $properties = [];

            foreach ($reflection->getProperties() as $prop) {
                $prop->setAccessible(true);
                try {
                    $propValue = $prop->getValue($value);
                    $properties[] = [
                        'key' => $prop->getName(),
                        'visibility' => $this->getVisibility($prop),
                        'value' => self::toDetailedStructure($propValue, $depth + 1, $maxDepth),
                    ];
                } catch (\Exception $e) {
                    $properties[] = [
                        'key' => $prop->getName(),
                        'visibility' => $this->getVisibility($prop),
                        'value' => [
                            'type' => 'error',
                            'value' => $e->getMessage(),
                            'isExpandable' => false,
                        ],
                    ];
                }
            }

            return [
                'type' => 'object',
                'class' => get_class($value),
                'value' => get_class($value) . '(' . count($properties) . ' properties)',
                'isExpandable' => count($properties) > 0,
                'properties' => $properties,
            ];
        }

        // Fallback for other types
        return [
            'type' => gettype($value),
            'value' => (string)$value,
            'isExpandable' => false,
        ];
    }

    /**
     * Get visibility modifier for a reflection property
     */
    private function getVisibility(\ReflectionProperty $prop): string
    {
        if ($prop->isPublic()) {
            return 'public';
        }
        if ($prop->isProtected()) {
            return 'protected';
        }
        return 'private';
    }

    /**
     * Format bytes as human-readable
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= 1024 ** $pow;

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Format stack trace
     */
    private static function formatStackTrace(array $trace): array
    {
        return array_map(function ($frame) {
            return [
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? 'unknown'),
                'args' => isset($frame['args']) ? count($frame['args']) : 0,
            ];
        }, $trace);
    }

    /**
     * Send payload to the debug server
     */
    private static function send(string $type, array $data): void
    {
        if (!self::$config['enabled']) {
            return;
        }

        $endpoint = self::$endpoint ?? self::$config['endpoint'];
        $origin = self::$origin ?? self::detectOrigin();

        $payload = [
            'type' => $type,
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
            'origin' => $origin,
            'data' => $data,
            'meta' => [
                'php_version' => PHP_VERSION,
                'sapi' => php_sapi_name(),
                'memory_usage' => self::formatBytes(memory_get_usage(true)),
            ],
        ];

        if (self::$config['async']) {
            self::sendAsync($endpoint, $payload);
        } else {
            self::sendSync($endpoint, $payload);
        }
    }

    /**
     * Send payload asynchronously (non-blocking)
     */
    private static function sendAsync(string $url, array $payload): void
    {
        $json = json_encode($payload);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $json,
                'timeout' => self::$config['timeout'],
                'ignore_errors' => true,
            ],
        ]);

        // Use a background request to not block execution
        @file_get_contents($url, false, $context);
    }

    /**
     * Send payload synchronously (blocking)
     */
    private static function sendSync(string $url, array $payload): void
    {
        $json = json_encode($payload);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $json,
                'timeout' => self::$config['timeout'],
            ],
        ]);

        try {
            @file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            // Silently fail if server is not available
        }
    }

    /**
     * Detect the project origin from composer.json or directory
     */
    private static function detectOrigin(): string
    {
        // Try to find composer.json
        $dir = getcwd();
        while ($dir !== '/' && $dir !== '\\') {
            if (file_exists($composerPath = $dir . '/composer.json')) {
                try {
                    $composer = json_decode(file_get_contents($composerPath), true);
                    if (isset($composer['name'])) {
                        return $composer['name'];
                    }
                } catch (\Exception $e) {
                    // Continue
                }
            }

            $dir = dirname($dir);
        }

        return basename(getcwd());
    }

    /**
     * Enable the debugger
     */
    public static function enable(): void
    {
        self::$config['enabled'] = true;
    }

    /**
     * Disable the debugger
     */
    public static function disable(): void
    {
        self::$config['enabled'] = false;
    }

    /**
     * Check if debugger is enabled
     */
    public static function isEnabled(): bool
    {
        return self::$config['enabled'];
    }
}
