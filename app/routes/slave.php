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
    function (Request $request, Response $response) {
        return $this->view->render($response, 'slave.twig');
    }
)->setName('slave');

/**
 *
 */
$app->post(
    '/slave',
    function (Request $request, Response $response) {
        $params = $request->getParsedBody();

        array_walk($params, function (&$p) {
            $p = trim($p);
        });

        if ($params['set'] == '' || $params['desc'] == '' || $params['data'] == '') {
            $this['flash']('RequiredFieldMissing', 'danger');
            return $response->withRedirect(
                $this->router->pathFor('slave', $params),
                200
            );
        }

        if (!preg_match('~^[a-zA-Z][a-zA-Z_0-9]*$~', $params['set'])) {
            $this['flash']('CodeRegexFailed', 'danger');
            return $response->withRedirect(
                $this->router->pathFor('slave', $params),
                200
            );
        }

        if ($params['order'] < 1) {
            $this['flash']('CodeOrderNotGEZero', 'danger');
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
        $this['editor']->putSlave('code_set', $params['set'], $params['desc']);
        // Parameter entries
        foreach ($data as $order => $code) {
            $this['editor']->putData(
                $params['set'],
                $this['editor']->native,
                $code,
                $code,
                0,
                0,
                ($order+1) * $params['order']
            );
        }

        return $response->withRedirect(
            $this->router->pathFor('list', array('set' => $params['set'])),
            200
        );
    }
)->setName('POST slave');
