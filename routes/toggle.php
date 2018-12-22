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
$app->post(
    '/toggle',
    function (Request $request, Response $response, array $args) {
        $params = array_merge(['set' => '', 'code' => ''], $request->getParsedBody());

        if ($this['editor']->get($params['set'], $this['editor']->native, $params['code'])) {
            $this['editor']->toggleActive($params['set'], $params['code']);
            Session::flash('CodeStateToggled');
        } else {
            Session::flash('CodeMissing|danger');
        }

        return $response->withRedirect(
            $this->router->pathFor('list', array('set' => $params['set'])),
            200
        );
    }
)->setName('toggle');
