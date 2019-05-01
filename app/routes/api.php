<?php
/**
 *
 */
use Slim\Http\Body;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *
 */
$app->group('/api', function () {
    $this->get(
        '/set/{set}[/{lang}]',
        function (Request $request, Response $response, $set, $lang = '') {
            $langSet = $this['editor']->fullSet($set, $lang ?? $this['editor']->native);
            $transform = $request->getQueryParam('transform');
            $translations = [];

            foreach ($langSet as $data) {
                if ($data['active']) {
                    switch ($transform) {
                        case 'l':
                        case 'lower':
                        case 'lowercase':
                            $translations[strtolower($data['code'])] = $data['desc'];
                            break;

                        case 'u':
                        case 'upper':
                        case 'uppercase':
                            $translations[uppercase($data['code'])] = $data['desc'];
                            break;

                        default:
                            $translations[$data['code']] = $data['desc'];
                            break;
                    }
                }
            }

            return $response->withJson($translations);
        }
    );

    /**
     * Toggle active/inactive
     */
    $this->post(
        '/toggle',
        function (Request $request, Response $response) {
            $params = array_merge(['set' => '', 'code' => ''], $request->getParsedBody());
            return $response->withJson(!!$this['editor']->toggleActive($params['set'], $params['code']));
        }
    );

    /**
     * Delete code
     */
    $this->post(
        '/delete',
        function (Request $request, Response $response) {
            $params = array_merge(['set' => '', 'code' => ''], $request->getParsedBody());
            return $response->withJson($this['editor']->remove($params['set'], $params['code']));
        }
    );
})->add(function (Request $request, Response $response, $next) {
    $response = $next($request, $response);

    $queryParams = $request->getQueryParams();

    $callback = $queryParams['jsonp'] ?? false;
    if (!$callback) {
        $callback = $queryParams['callback'] ?? false;
    }

    if ($callback) {
        // Remember content
        $response->getBody()->rewind();
        $json = $response->getBody()->getContents();
        // Recreate content
        $response = $response->withBody(new Body(fopen('php://temp', 'r+')));
        $response->getBody()->write($callback . '(' . $json . ')');

        return $response->withHeader('Content-Type', 'application/javascript;charset=utf-8');
    }

    return $response;
});
