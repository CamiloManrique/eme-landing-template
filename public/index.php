<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Dotenv\Dotenv;
use Illuminate\Validation\Rule;
use Illuminate\Validation\DatabasePresenceVerifier;

$dotenv = new Dotenv();
$dotenv->load(ROOT_DIR . '/.env');

$app_env = isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : 'local';
$app_debug = $_ENV['APP_DEBUG'] === 'true' ? true : false;
$db_connection = isset($_ENV['DB_CONNECTION']) ? $_ENV['DB_CONNECTION'] : "pdo_mysql";
$db_port = isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 3306;

$configuration = [
    'settings' => [
        'displayErrorDetails' => $app_debug,
        'db' => [
            'driver' => $db_connection,
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'port' => $db_port,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];

$container = new \Slim\Container($configuration);

$container['view'] = function ($container) use ($app_env) {
    $cache = $app_env === 'production' ? ROOT_DIR.'/cache' : false;
    $view = new \Slim\Views\Twig(ROOT_DIR.'/views', [
        'cache' => $cache
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $view->addExtension(new \Classes\Twig\AssetExtension);

    return $view;
};

$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();


    return $capsule;
};

$container['translation'] = function ($container){
    $filesystem = new Illuminate\Filesystem\Filesystem();
    $loader = new Illuminate\Translation\FileLoader($filesystem, dirname(dirname(__FILE__)) . '/lang');
    $loader->addNamespace('lang', dirname(dirname(__FILE__)) . '/lang');
    $loader->load($lang = 'es', $group = 'validation', $namespace = 'lang');
    $loader->load($lang = 'en', $group = 'validation', $namespace = 'lang');

    $factory = new Illuminate\Translation\Translator($loader, 'es');
    return $factory;
};

$container['validationFactory'] = function ($container){

    $translation = $container->get('translation');

    $capsule = $container->get('db');
    $connection_resolver = $capsule->getDatabaseManager();

    $database_verifier = new DatabasePresenceVerifier($connection_resolver);

    $validator = new Illuminate\Validation\Factory($translation);
    $validator->setPresenceVerifier($database_verifier);

    return $validator;
};

$app = new \Slim\App($container);

$app->get('/hello/{name}', function (Request $request, Response $response, $args){
    $items = ["Item 1", "Item 2", "Item 3"];
    return $this->view->render($response, 'example.twig', [
        'name' => $args['name'],
        'items' => $items
    ]);
});

$app->any('/variables/example', function (Request $request, Response $response){
    $data = ["data" => $request->getParams()];
    $username = $request->getParam('username');
    $data = array_merge($data, ['username' => $username]);
    return $response->withJson($data, 200);
});

$app->any('/redirect/example', function (Request $request, Response $response){
    return $response->withRedirect(route('new-url'), 303);
});

$app->any('/validation/example', function (Request $request, Response $response){
    $validationFactory = $this->get('validationFactory');
    $validator = $validationFactory->make($request->getParams(), [
        "username" => 'required',
        "email" => ['required', 'email', Rule::unique('users', 'email')]
    ]);

    if($validator->fails()){
        return return_validation_errors($response, $validator);
    }

    return $response->withJson(['success' => true]);

});

$app->run();
