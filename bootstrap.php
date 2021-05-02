<?php
require __DIR__.'/vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

\Dotenv\Dotenv::createImmutable(__DIR__)->load();
$config = \Noodlehaus\Config::load(__DIR__ . '/config.php');

function config(string $key, $default = null)
{
    static $config;

    if ($config == null) {
        $config = \Noodlehaus\Config::load(__DIR__ . '/config.php');
    }

    return $config->get($key, $default);
}

function env(string $key, $default = null)
{
    if (array_key_exists($key, $_ENV) == false) {
        return $default;
    }

    $value = $_ENV[$key];

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'empty':
        case '(empty)':
            return '';

        case 'null':
        case '(null)':
            return null;
    }

    if (substr($value, -1) === '"' && substr($value, 0, 1) === '"') {
        return substr($value, 1, -1);
    }

    return $value;
}
