<?php
/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;
use App\Session;

/**
 *
 */
$app->map(
    ['GET', 'POST'],
    '/login',
    function (Request $request, Response $response) {
        if ($request->isPost()) {
            $params = $request->getParsedBody();

            $user = array_key_exists('user', $params) ? $params['user'] : '';
            $pw = array_key_exists('pw', $params) ? $params['pw'] : '';

            $data = $this['editor']->checkLogin($user, $pw);

            if (!empty($data)) {
                Session::login($user);
                Session::set('app', $data);

                // Session::set('level', $this['editor']->param('code_acl', $user));
                $this['flash']('LoggedIn');
                return $response->withRedirect($this->router->pathFor('index'));
            } else {
                $this['flash']('LoginInvalid', 'danger');
                return $response->withRedirect($this->router->pathFor('login'));
            }
        }

        return $this->view->render($response, 'login.twig');
    }
)->setName('login');

/**
 *
 */
$app->get(
    '/logout',
    function (Request $request, Response $response) {
        Session::logout();
        Session::destroy();
        $this['flash']('LoggedOut');
        return $response->withRedirect($this->router->pathFor('index'));
    }
)->setName('logout');
