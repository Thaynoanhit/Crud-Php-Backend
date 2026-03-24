<?php

use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

function envValue(string $key, ?string $default = null): ?string
{
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }

    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return (string) $value;
    }

    return $default;
}

return [
    'paths' => [
        'migrations' => 'db/migrations',
        'seeds' => 'db/seeds'
    ],

    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',

        'development' => [
            'adapter' => 'mysql',
            'host' => envValue('DB_HOST', '127.0.0.1'),
            'name' => envValue('DB_NAME', 'app'),
            'user' => envValue('DB_USER', 'user'),
            'pass' => envValue('DB_PASS', 'user'),
            'port' => (int) envValue('DB_PORT', '3308'),
            'charset' => 'utf8',
        ],
    ],

    'version_order' => 'creation'
];