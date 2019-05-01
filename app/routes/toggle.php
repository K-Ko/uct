<?php
/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *
 */
$app->post(
    '/toggle',
    function (Request $request, Response $response) {
        $params = array_merge(['set' => '', 'code' => ''], $request->getParsedBody());

        if ($this['editor']->get($params['set'], $this['editor']->native, $params['code'])) {
            $this['editor']->toggleActive($params['set'], $params['code']);
            $this['flash']('CodeStateToggled');
        } else {
            $this['flash']('CodeMissing', 'danger');
        }

        return $response->withRedirect(
            $this->router->pathFor('list', [ 'set' => $params['set'] ]),
            200
        );
    }
)->setName('toggle');
