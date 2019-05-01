<?php
/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Homepage
 */
$app->get(
    '/',
    function (Request $request, Response $response) {
        $e = $this['editor'];

        $counts    = $e->getCount();
        $lang_rows = $e->languageSet('code_lang', $this['language']);
        $set_rows  = $e->languageSet('code_set');

        $totals = [];
        $todo_count = $todo_rows = 0;

        $set_rows = array_reverse($set_rows);

        foreach ($set_rows as $id => &$row) {
            if ($e->isSystemSet($row['code'])) {
                unset($set_rows[$id]);
                continue;
            }

            $admin = $e->adminGet($row['code']);

            $row['admin'] = $admin;

            $nat_count = $_=&$counts[$row['code']][$e->native] ?: 0;
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

        return $this['view']->render(
            $response,
            'index.twig',
            [
                'lang_rows'  => $lang_rows,
                'set_rows'   => $set_rows,
                'totals'     => $totals,
                'todo_count' => $todo_count,
                'todo_rows'  => $todo_rows
            ]
        );
    }
)->setName('index');

/**
 * Called from code select form
 */
$app->post(
    '/',
    function (Request $request, Response $response) {
        $params = $request->getParsedBody();

        $f = $this['pathFor'];
        return $response->withRedirect(
            empty($params['set']) ? $f('index') : $f('list', $params),
            200
        );
    }
);
