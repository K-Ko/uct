<?php
/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Catch missing route specific files to avoid 404s
 */

$app->get('/css/{name}.css', function (Request $request, Response $response, $name) {
    return $response
        ->withHeader('Content-Type', 'text/css')
        ->write('/* /css/' . $name . '.css */');
});

$app->get('/js/{name}.js', function (Request $request, Response $response, $name) {
    return $response
        ->withHeader('Content-Type', 'application/javascript')
        ->write('/* /js/' . $name . '.js */');
});
