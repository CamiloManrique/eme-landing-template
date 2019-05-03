<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Dotenv\Dotenv;

require '../../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../../.env');

$app_debug = $_ENV['APP_DEBUG'] === 'true' ? true : false;

$configuration = [
    'settings' => [
        'displayErrorDetails' => $app_debug,
        'db' => [
            'driver' => $_ENV['DB_CONNECTION'],
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'port' => $_ENV['DB_PORT'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];

$container = new \Slim\Container($configuration);

$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();


    return $capsule;
};

$app = new \Slim\App($container);

$app->any('/variables/example', function (Request $request, Response $response){
    $data = ["data" => $request->getParams()];
    $username = $request->getParam('username');
    $data = array_merge($data, ['username' => $username]);
    return $response->withJson($data, 200);
});

$app->any('/redirect/example', function (Request $request, Response $response){
    return $response->withRedirect(route('new-url'), 303);
});

$app->run();
