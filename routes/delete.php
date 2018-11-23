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
    '/delete',
    function (Request $request, Response $response, array $args) {
        $params = array_merge(['set' => '', 'code' => ''], $request->getParsedBody());
        $url = $this->router->pathFor('list', array('set' => $params['set']));

        if (!$this['editor']->get('code_set', $this['editor']->native, $params['set'])) {
            Session::flash('CodesetMissing|danger');
            return $response->withRedirect($url, 200);
        }

        if ($params['code'] == '') {
            Session::flash('CodeMissing|danger');
            return $response->withRedirect($url, 200);
        }

        $nat_exists = $this['editor']->get($params['set'], $this['editor']->native, $params['code']);

        if (!$nat_exists) {
            Session::flash('CodeMissing|danger');
            return $response->withRedirect($url, 200);
        }

        $this['editor']->removeData($params['set'], $params['code']);
        Session::flash('CodeDeleted');

        if (isset($params['next'])) {
            // Edit next code
            return $response->withRedirect($params['next'], null, 200);
        }

        return $response->withRedirect($url, 200);
    }
)->setName('POST delete');
