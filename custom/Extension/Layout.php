<?php
/**
 *
 */
namespace App\Extension;

/**
 * Definitions for Password self-service extension
 */
use App\Extension;
use App\Session;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class Layout extends Extension
{

    public static $name = 'Layout';
    public static $description = 'Custom layout by KKo';
    public static $version = '1.0.0';
    public static $link = '';


    public function register(App $app)
    {
        // Define route(s)
        // Return content of /custom/extension/layout/style.css
        $app->get('/layout/style.css', function (Request $request, Response $response) {
            return $response
                ->withContentType('text/css')
                ->write(file_get_contents($this->path . '/Layout/style.css'));
        })->setName('Layout styles');

        // Return content of /app/extension/layout/script.js
        $app->get('/layout/script.js', function (Request $request, Response $response) {
            return $response
                ->withContentType('application/javascript')
                ->write(file_get_contents($this->path . '/Layout/script.js'));
        })->setName('Layout scripts');

        // Register extension point(s)
        return [ 'stylesheets', 'scripts' ];
    }
}
