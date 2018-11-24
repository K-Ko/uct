<?php
/**
 *
 */
use Core\I18N;
use Core\MySQLi;
use Core\Session;
use Dotenv\Dotenv;
use Pimple\Container;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use UCT\Editor;
use UCT\Middleware;

/**
 *
 */
$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], '/');

require_once 'vendor/autoload.php';

Session::$ttl = 7 * 24 * 60 * 60;
Session::start();
Session::checkRemembered();

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

if (getenv('ENVIRONMENT') == 'development') {
    ini_set('display_errors', 1);
    error_reporting(-1);

    /**
     * Debug display
     */
    function d()
    {
        echo '<pre>';
        foreach (func_get_args() as $arg) {
            print_r($arg);
        }
        exit;
    }
}

$settings = [
    'addContentLengthHeader' => false,
    'determineRouteBeforeAppMiddleware' => true
];

// Configure caching BEFORE
if (getenv('ENVIRONMENT') == 'development') {
    $settings['displayErrorDetails'] = true;
    $settings['Debug'] = true;
} else {
    $settings['routerCacheFile'] = __DIR__ . '/cache/routes.cache.php';
}

$app = new App([ 'settings' => $settings ]);

unset($settings);

// Fetch DI Container
$container = $app->getContainer();

$container['baseDir'] = function ($c) {
    return __DIR__;
};

$container['cacheDir'] = function ($c) {
    return $c['baseDir'].'/cache';
};

// Register provider
$container['config'] = function ($c) {
    // Build configuration
    $env = array_merge(
        [
            'CODESETS' => [],
            'LAYOUT'   => 'default',
            'LOGIN'    => null,
        ],
        $_ENV
    );

    if (Session::loggedIn()) {
        $env['CODESETS'] = [];
    } elseif (!empty($env['CODESETS'])) {
        $env['CODESETS'] = explode(',', $env['CODESETS']);
    }

    list($env['VERSION'], $env['VERSION_DATE']) =
        file($c['baseDir'].'/.version', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    return new Container($env);
};

$container['db'] = function ($c) {
    $db = @new MySQLi(
        $c['config']['DBHOST'], $c['config']['DBUSER'], $c['config']['DBPASS'], $c['config']['DBNAME']
    );

    if ($db->connect_error) {
        throw new Exception($db->connect_error);
    }

    $db->verbose = (getenv('ENVIRONMENT') == 'development');

    $db->query('SET NAMES "utf8"');
    $db->query('SET CHARACTER SET utf8');

    return $db;
};

$container['editor'] = function ($c) {
    try {
        return new Editor(
            [ 'db' => $c['db'], 'table' => $c['config']['DBTABLE'] ]
        );
    } catch (Exception $e) {
        die('<h1>Something went wrong</h1><pre>' . $e->getMessage());
    }
};

$container['i18n'] = function ($c) {
    $i18n = new I18N($c['editor'], 'code_editor');
    $i18n->setLanguage(Session::get('language', Session::getCookie('language')));
    return $i18n;
};

// Register Twig View helper
$container['view'] = function ($c) {
    $settings = [
        'autoescape' => false,
        'cache' => new Twig_Cache_Filesystem(
            $c['cacheDir'],
            Twig_Cache_Filesystem::FORCE_BYTECODE_INVALIDATION
        )
    ];

    if (getenv('ENVIRONMENT') == 'development') {
        // Configure debug and verbose
        $settings['debug'] = true;
        $settings['strict_variables'] = true;
        $c['profiler'] = function ($c) {
            return new Twig_Profiler_Profile();
       };
    }

    $twig = new Twig($c['baseDir'].'/tpl/default', $settings);

    if ($c['config']['LAYOUT'] != 'default') {
        $dir = $c['baseDir'].'/tpl/'.$c['config']['LAYOUT'];
        if (is_dir($dir)) {
            $twig->getLoader()->prependPath($dir);
        } else {
            $c['config']['LAYOUT'] = 'default';
            Session::flash('Missing layout directory: ' . $dir . '|danger');
        }
    }

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $twig->addExtension(new TwigExtension($c['router'], $basePath));

    if (getenv('ENVIRONMENT') == 'development') {
        $twig->addExtension(new Twig_Extension_Profiler($c['profiler']));
    }

    $twigEnv = $twig->getEnvironment();

    // Translation
    $twigEnv->addGlobal('t', $c['i18n']);

    $twigEnv->addFunction(
        new Twig_SimpleFunction(
            'pathFor',
            function ($name, $args = []) use ($c) {
                // Remove trailing slash, mostly if lang2 is not set
                return rtrim($c->router->pathFor($name, $args), '/');
            }
        )
    );

    return $twig;
};

/**
 * Initial language
 */
if (!Session::get('language')) {
    if (!Session::getCookie('language')) {
        // Detect language from browser settings, fallback to native language
        $locale = Locale::lookup(
            $container['editor']->activeLanguageSets(),
            Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']),
            true,
            $container['editor']->native
        );
        Session::setCookie('language', $locale, 365 * 24 * 60 * 60);
    }
    Session::set('language', Session::getCookie('language'));
}

/**
 * Routes and Middleware
 */
foreach (glob($container['baseDir'].'/routes/*.php') as $file) {
    include $file;
}

$app->add(new Middleware($container));

/**
 * Go
 */
$app->run();

if (getenv('ENVIRONMENT') == 'development') {
    $dumper = new Twig_Profiler_Dumper_Text();
    echo '<div class="container"><hr><pre>';
    echo $dumper->dump($container['profiler']);
    echo '<!-- ';
    echo '----------------------------------------------------------------------', PHP_EOL;
    print_r($container['db']->queries);
    echo '-->';
}
