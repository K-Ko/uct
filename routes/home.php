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
    '/',
    function (Request $request, Response $response, array $args) {
        $counts    = $this['editor']->getCount();
        $lang_rows = $this['editor']->languageSet('code_lang', Session::get('language'));
        $set_rows  = $this['editor']->languageSet('code_set');

        $totals = [];
        $todo_count = $todo_rows = 0;

        foreach ($set_rows as $id => &$row) {
            $admin = $this['editor']->adminGet($row['code']);

            if ((!Session::loggedIn() && ($admin['param'] || !$row['active'])) ||
                $this->config['CODESETS'] && !in_array($row['code'], $this->config['CODESETS']) ||
                $row['order'] < 0
            ) {
                unset($set_rows[$id]);
                continue;
            }

            $row['admin'] = $admin;

            $nat_count = $_=&$counts[$row['code']][$this['editor']->native] ?: 0;
            $row['count'] = $nat_count;

            foreach ($lang_rows as $lang_row) {
                if (!$lang_row['active']) {
                    continue;
                }

                $code_count = $_=&$counts[$row['code']][$lang_row['code']] ?: 0;

                if ($code_count != $nat_count) {
                    $todo_count++;
                    $todo_rows += $nat_count - $code_count;
                }
                $row['cols'][] = [
                    'set' => $row['code'],
                    'lang2' => $lang_row['code'],
                    'count' => $code_count,
                ];

                if (!isset($totals[$lang_row['code']])) {
                    $totals[$lang_row['code']] = 1;
                } else {
                    $totals[$lang_row['code']] += $code_count;
                }
            }
        }

        return $this->view->render(
            $response,
            'home.twig',
            [
                'lang_rows'  => $lang_rows,
                'set_rows'   => $set_rows,
                'totals'     => $totals,
                'todo_count' => $todo_count,
                'todo_rows'  => $todo_rows
            ]
        );
    }
)->setName('home');

/**
 *
 */
$app->post(
    '/',
    function (Request $request, Response $response, array $args) {
        // Remove empty parameters
        $params = array_filter($request->getParsedBody(), function ($p) {
            return !!$p;
        });

        return empty($params['set'])
             ? $response->withRedirect($this->router->pathFor('home'), 200)
             : $response->withRedirect($this->router->pathFor('list', $params), 200);
    }
);
