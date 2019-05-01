<?php
/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *
 */
$app->get(
    '/applications',
    function (Request $request, Response $response) {
        return $this->view->render(
            $response,
            'app/list.twig',
            [ 'rows' => $this['editor']->paramSet('code_app') ]
        );
    }
)->setName('app-list');

/**
 *
 */
$app->get(
    '/application[/{id}]',
    function (Request $request, Response $response, $id = null) {
        $data = [];

        return $this->view->render($response, 'app/edit.twig', $data);
    }
)->setName('app-edit');

/**
 *
 */
$app->post(
    '/application[/{id}]',
    function (Request $request, Response $response, $id = null) {
        $params = $request->getParsedBody();



        return $response->withRedirect($this->router->pathFor('app-list'), 200);
    }
);

/**
 *
 */
$app->post(
    '/application/toggle/{id}',
    function (Request $request, Response $response, $id) {
        if ($id == 0) {
            $this['flash']('SystemAppIsProtected', 'danger');
        }

        return $response->withRedirect($this->router->pathFor('app-list'), 200);
    }
)->setName('app-toggle');

/**
 *
 */
$app->post(
    '/application/delete/{id}',
    function (Request $request, Response $response, $id) {
        if ($id == 0) {
            $this['flash']('SystemAppIsProtected', 'danger');
        }

        return $response->withRedirect(
            $this->router->pathFor('list', [ 'set' => $params['set'] ]),
            200
        );
    }
)->setName('app-delete');
