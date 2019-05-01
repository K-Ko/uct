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

class Pss extends Extension
{

    public static $name = 'Password self-service';
    public static $description = 'User can change its own password';
    public static $version = '1.0.0';
    public static $link = '';


    public function register(App $app)
    {
        // Define route(s)
        $app->get(
            '/password-self-service',
            function (Request $request, Response $response) {
                $this['view']['route'] = 'pss';
                // Render template
                $this['view']->render($response, 'Pss/form.twig');
                return $response;
            }
        )->setName('Extension Pss');

        $app->post(
            '/password-self-service',
            function (Request $request, Response $response) {
                $data = $request->getParsedBody();

                if ($this['editor']->param('code_user', $this['user']) != sha1($data['pw'])) {
                    $this['flash']('code_ext::pss_InvalidPassword', 'danger');
                    return $response->withRedirect($this->router->pathFor('Extension Pss'));
                }


                if ($data['pw1'] != '' && $data['pw1'] === $data['pw2']) {
                    $this['editor']->putParam('code_user', $this['user'], sha1($data['pw1']));
                    $this['flash']('code_ext::pss_PasswordChanged');
                    return $response->withRedirect($this->router->pathFor('index'));
                }

                // Reload
                $this['flash']('code_ext::pss_PasswordsNotEqual', 'warning');
                return $response->withRedirect($this->router->pathFor('Extension Pss'));
            }
        );

        $app->get(self::SCRIPT, function (Request $request, Response $response) {
            return $response
                ->withHeader('Content-type', 'application/javascript')
                ->write(file_get_contents($this['baseDir'] . '/app/src/App/Extension/Pss/script.js'));
        });

        // Register extension point(s)
        return [ 'nav-right-before', 'scripts' ];
    }

    public function contentScripts()
    {
        return '<script src="' . self::SCRIPT . '"></script>';
    }

    const SCRIPT = '/pss/script.js';
}
