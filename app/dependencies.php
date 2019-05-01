<?php
/**
 *
 */
use App\Extensions;
use App\MySQLi;
use App\Session;
use Pimple\Container;
use Slim\Flash\Messages;
use Knlv\Slim\Views\TwigMessages;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\StringLoaderExtension;
use Twig\Extension\DebugExtension;
use Twig\I18NMarkdown;
use UCT\Editor;

/**
 *
 */
$container = $app->getContainer();

$container['foundHandler'] = function () {
    return new RequestResponseArgs();
};

$container['baseDir'] = function ($c) {
    return dirname(__DIR__);
};

$container['cacheDir'] = function ($c) {
    return $c['baseDir'].'/cache';
};

$container['develop'] = function ($c) {
    return file_exists($c['baseDir'] . '/.develop');
};

// Register provider
$container['config'] = function ($c) {
    $config = include $c['baseDir'] . '/config.local.php';

    list($config['version'], $config['version_date']) =
        file($c['baseDir'].'/.version', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    return new Container($config);
};

$container['db'] = function ($c) {
    $cfg = $c['config']['database'];

    $db = @new MySQLi($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name']);

    if ($db->connect_error) {
        throw new Exception($db->connect_error);
    }

    $db->verbose = $c['develop'];

    $db->set_charset("utf8");
    // $db->query('SET NAMES "utf8"');
    // $db->query('SET CHARACTER SET utf8');

    return $db;
};

$container['editor'] = function ($c) {
    try {
        return new Editor(
            [
                'db' => $c['db'],
                'table' => $c['config']['database']['table'],
                'app' => $c['app']['id']
            ]
        );
    } catch (Exception $e) {
        die('<h1>Something went wrong</h1><pre>' . $e->getMessage());
    }
};

$container['i18n'] = function ($c) {
    return new I18NMarkdown(
        $c['editor'],
        [ 'set' => 'code_editor', 'language' => $c['language'] ]
    );
};

// Register Twig View helper
$container['view'] = function ($c) {
    $settings = [
        'autoescape' => false,
        'cache' => new Twig_Cache_Filesystem(
            $c['cacheDir'],
            Twig_Cache_Filesystem::FORCE_BYTECODE_INVALIDATION
        ),
        'strict_variables' => $c['develop'],
        'debug' => $c['develop'],
    ];

    $twig = new Twig(
        [
            $c['baseDir'].'/custom/tpl',
            $c['baseDir'].'/app/tpl',
            $c['baseDir'].'/app/src/App/Extension',
            $c['baseDir'].'/custom/Extension'
        ],
        $settings
    );

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $twig->addExtension(new TwigExtension($c['router'], $basePath));
    $twig->addExtension(new StringLoaderExtension());
    $twig->addExtension(new TwigMessages(
        new Messages()
    ));

    if ($c['develop']) {
        $twig->addExtension(new DebugExtension());

        $c['profiler'] = function ($c) {
            return new Twig_Profiler_Profile();
        };
        $twig->addExtension(new Twig_Extension_Profiler($c['profiler']));
    }

    $twigEnv = $twig->getEnvironment();

    // Translation
    $twigEnv->addGlobal('t', $c['i18n']);

    $twigEnv->addFunction(new Twig_SimpleFunction('pathFor', $c['pathFor']));

    return $twig;
};

// Wrap Messages->addMessage()
$container['flash'] = function ($c) {
    $msgs = new Messages();
    return function ($message, $type = 'success') use ($c, $msgs) {
        $msgs->addMessage('', [ $message, $type ]);
    };
};

$container['pathFor'] = function ($c) {
    return function ($name, $args = []) use ($c) {
        $path = $c->router->pathFor($name, $args);
        // Remove trailing slash, mostly if lang2 is not set
        return $path == '/' ? $path : rtrim($path, '/');
    };
};

$container['language'] = function ($c) {
    if (!Session::get('language')) {
        if (!Session::getCookie('language')) {
            // Detect language from browser settings, fallback to native language
            $locale = Locale::lookup(
                $c['editor']->activeLanguageSets(),
                Locale::acceptFromHttp(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : ''),
                true,
                $c['editor']->native
            );
            Session::setCookie('language', $locale, 365 * 24 * 60 * 60);
        }
        Session::set('language', Session::getCookie('language'));
    }

    return Session::get('language');
};

$container['user'] = function () {
    return Session::get(Session::LOGIN);
};

$container['app'] = function () {
    return Session::get('app');
};

$container['can'] = function ($c) {
    $level = $c['editor']->param('code_acl', $c['user']);

    return [
        'display'   => $level >= 1,
        'translate' => $level >= 2,
        'edit'      => $level >= 3,
        'maintain'  => $level >= 4,
        'admin'     => $level >= 9,
    ];
};

$container['extensions'] = function ($c) {
    return new Extensions($c);
};
