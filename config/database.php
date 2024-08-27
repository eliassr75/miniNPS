<?php
$parsed = parse_ini_file('../.env', true);

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'pgsql',
    'host' => $parsed['DB_HOST'],
    'port' => $parsed['DB_PORT'],
    'database' => $parsed['DB_NAME'],
    'username' => $parsed['DB_NAME'],
    'password' => $parsed['DB_PASSWORD'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'timezone' => $parsed['DB_TIMEZONE'],
    'schema' => 'public',
    'sslmode' => 'prefer'
]);

define('MAIL_HOST', $parsed['MAIL_HOST']);
define('MAIL_PORT', $parsed['MAIL_PORT']);
define('MAIL_USERNAME', $parsed['MAIL_USERNAME']);
define('MAIL_PASSWORD', $parsed['MAIL_PASSWORD']);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();
