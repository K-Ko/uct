<?php
/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;
use Core\Session;

/**
 *
 */
$app->get(
    '/login',
    function (Request $request, Response $response, array $args) {
        return $this->view->render($response, 'login.html');
    }
)->setName('login');

/**
 *
 */
$app->post(
    '/login',
    function (Request $request, Response $response, array $args) {
        $params = $request->getParsedBody();
        if (isset($params['pw']) && sha1($params['pw']) == $this->config['LOGIN']) {
            Session::login();
            Session::flash('LoggedIn');
            return $response->withRedirect($this->router->pathFor('home'));
        } else {
            Session::flash('LoginInvalid|danger');
            return $response->withRedirect($this->router->pathFor('login'));
        }
    }
);

/**
 *
 */
$app->get(
    '/logout',
    function (Request $request, Response $response, array $args) {
        Session::logout();
        Session::flash('LoggedOut');
        return $response->withRedirect($this->router->pathFor('home'));
    }
)->setName('logout');
