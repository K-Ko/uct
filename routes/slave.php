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
    '/slave',
    function (Request $request, Response $response, array $args) {
        return $this->view->render($response, 'slave.html');
    }
)->setName('slave');

/**
 *
 */
$app->post(
    '/slave',
    function (Request $request, Response $response, array $args) {
        $params = $request->getParsedBody();

        array_walk($params, function (&$p) {
            $p = trim($p);
        });

        if ($params['set'] == '' || $params['desc'] == '' || $params['data'] == '') {
            Session::flash('RequiredFieldMissing|danger');
            return $response->withRedirect(
                $this->router->pathFor('slave', $params),
                200
            );
        }

        if (!preg_match('~^[a-zA-Z][a-zA-Z_0-9]*$~', $params['set'])) {
            Session::flash('CodeRegexFailed|danger');
            return $response->withRedirect(
                $this->router->pathFor('slave', $params),
                200
            );
        }

        if ($params['order'] < 1) {
            Session::flash('CodeOrderNotGEZero|danger');
            return $response->withRedirect(
                $this->router->pathFor('slave', $params),
                200
            );
        }

        preg_match_all('~^.+$~m', $params['data'], $data, PREG_SET_ORDER);

        // Trim data lines
        array_walk($data, function (&$d) {
            $d = trim($d[0]);
        });
        // Filter empty lines
        $data = array_filter($data, function ($d) {
            return $d != '';
        });

        // Admin entry
        $this['editor']->adminPut($params['set'], ['slave' => 1]);
        // Code set entry
        $this['editor']->slave('code_set', $params['set'], $params['desc']);
        // Parameter entries
        foreach ($data as $order => $code) {
            $this['editor']->put(
                $params['set'],
                $this['editor']->native,
                $code,
                $code,
                ($order+1) * $params['order']
            );
        }

        return $response->withRedirect(
            $this->router->pathFor('list', array('set' => $params['set'])),
            200
        );
    }
)->setName('POST slave');
