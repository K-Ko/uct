<?php
/**
 *
 */
// use Core\Middleware as CoreMiddleware;
use App\Session;
use Slim\App;
// use UCT\Middleware as UCTMiddleware;
use UCT\UCT;

clearstatcache();

/**
 *
 */
$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], '/');

require_once __DIR__ . '/vendor/autoload.php';

Session::$ttl = 7 * 24 * 60 * 60;
Session::start();
Session::checkRemembered();

$develop = file_exists(__DIR__ . '/.develop');

if ($develop) {
    ini_set('display_errors', 1);
    error_reporting(-1);

    /**
     * Debug params and die
     */
    function dbg()
    {
        echo '<pre>';
        foreach (func_get_args() as $arg) {
            print_r($arg);
        }
        echo '</pre>';
    }

    /**
     * Debug params and die
     */
    function d()
    {
        echo '<pre>';
        foreach (func_get_args() as $arg) {
            print_r($arg);
        }
        die('</pre>');
    }
}

$settings = [
    /**
     * Slim app settings
     */
    'settings' => [
        'addContentLengthHeader' => false,
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => $develop,
        'Debug' => $develop,
        'routerCacheFile' => $develop ? false : __DIR__ . '/../cache/routes.cache.php',
    ],
];

$app = new App([ 'settings' => $settings['settings'] ]);

require __DIR__ . '/app/dependencies.php';

/**
 * Routes
 */
foreach (glob($container['baseDir'].'/app/routes/*.php') as $file) {
    include $file;
}

// Load all extension definitions
foreach (array_keys(require $container['baseDir'] . '/app/extensions.php') as $name) {
    $class = 'App\Extension\\'  . $name;
    $container['extensions']->register($app, new $class($container));
}

require __DIR__ . '/app/middleware.php';

/**
 * Go
 */
$app->run();
