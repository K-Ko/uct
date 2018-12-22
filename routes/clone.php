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
    '/clone',
    function (Request $request, Response $response, array $args) {
        $params = array_merge(['set' => '', 'code' => ''], $request->getParsedBody());

        if ($code = $this['editor']->clone($params['set'], $params['code'])) {
            // Remember state for automatic switch to rename code mode
            Session::set('cloned', true);

            return $response->withRedirect(
                $this->router->pathFor('edit', ['set' => $params['set'], 'code' => $code]),
                200
            );
        }

        Session::flash('NotSaved|danger');

        return $response->withRedirect(
            $this->router->pathFor('list', ['set' => $params['set']]),
            200
        );
    }
)->setName('clone');
