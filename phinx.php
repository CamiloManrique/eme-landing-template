<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection(array(
    'driver' => $_ENV['DB_CONNECTION'],
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'port' => $_ENV['DB_PORT'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
));

$capsule->setAsGlobal();

$pdo = $capsule->getDatabaseManager()->getPdo();


return [
    'paths' => [
        'migrations' => __DIR__.'/php/database/migrations',
        'seeds' => __DIR__.'/php/database/seeds',
    ],
    'environments' => [
        'default_database' => 'production',
        'production' => [
            'name' => $_ENV['DB_DATABASE'],
            'connection' => $pdo,
        ]
    ]
];