<?php
/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;
use App\Session;

/**
 *
 */
$app->get(
    '/detail/{set}[/{code}[/{lang}[/{lang2}]]]',
    function (Request $request, Response $response, $set, $code = '', $lang = '', $lang2 = '') {
        $e = $this['editor'];

        $native = $e->native;
        $language = $this['language'];

        if ($clone = $request->getQueryParam('clone')) {
            $suffix = '_' . strtoupper($e->desc('code_editor', $language, 'Copy'));
        } else {
            $suffix = '';
        }

        $data = array_merge(
            [
                'set'      => $set,
                'code'     => $code ?: $clone . $suffix,
                'lang'     => $lang,
                'lang2'    => $lang2,
                'nav'      => null,
                'quantity' => 0,
                'var'      => 0,
                'order'    => 0,
                'active'   => 1,
                'cloned'   => $clone != ''
            ],
            $e->adminGet($set)
        );

        $data['admin'] = $set == 'code_set' ? $e->adminGet($code) : null;

        if ($code == '') {
            // Add new code, No navigation!
            $data['nav'] = ['count' => 0];
        } else {
            $data['nav'] = $e->getNav($set, $code);
            if (!$data['nav']) {
                $this['flash']('InvalidCode', 'danger');
                $path = $this->router->pathFor('list', [ 'set' => $set ]);
                return $response->withRedirect($path, 200);
            }
        }

        $codeData = $e->get($set, $native, $code ?: $clone);

        if (!empty($codeData)) {
            $data = array_merge($data, $codeData);
            $data['hint'] = $e->getHint($set, $code, $this['language']);
        }

        $data['code_admin'] = $e->adminGet($set);

        if ($data['code_admin']['param']) {
            $data['code_admin']['desc'] = $e->desc('code_editor', $language, '_'.$set);

            // Parameter set, only native language
            $data['lang_rows'] = [
                [
                    'code'   => $native,
                    'desc'   => $e->desc('code_lang', $language, $native),
                    'order'  => 0,
                    'active' => 1
                ]
            ];
        } elseif ($lang2 == '') {
            // All languages
            $data['lang_rows'] = $e->languageSet('code_lang', $language);
        } else {
            // 2 languages only
            $data['lang_rows'] = [
                [
                    'code'   => $lang,
                    'desc'   => $e->desc('code_lang', $language, $lang),
                    'order'  => 0,
                    'active' => 1
                ],
                [
                    'code'   => $lang2,
                    'desc'   => $e->desc('code_lang', $language, $lang2),
                    'order'  => 0,
                    'active' => 1
                ]
            ];
        }

        foreach ($data['lang_rows'] as $id => &$row) {
            if (!$row['active']) {
                unset($data['lang_rows'][$id]);
                continue;
            }

            $row['name'] = ucfirst($row['desc']);
            $row['desc'] = $e->data($set, $row['code'], $code ?: $clone);
            $row['hint'] = $e->getHint($set, $code ?: $clone, $row['code']);
        }

        // d($data);

        $data['code_set_hint'] = $this['i18n']->h(
            $e->getHint('code_set', $set, $language)
        );
        $data['desc'] = $e->desc('code_set', null, $set);
        $data['route'] = 'detail';

        return $this->view->render($response, 'detail.twig', $data);
    }
)->setName('edit');

/**
 *
 */
$app->get(
    '/detail',
    function (Request $request, Response $response) {
        return $response->withRedirect($this->router->pathFor('index'), 200);
    }
);

/**
 *
 */
$app->post(
    '/detail',
    function (Request $request, Response $response) {
        $e = $this['editor'];

        $params = $request->getParsedBody();

        array_walk($params, function (&$p) {
            is_scalar($p) && $p = trim($p);
        });

        if (!preg_match('~^-?[0-9]+$~', $params['order'])) {
            $this['flash']('CodeOrderNotNumeric', 'danger');
            return $response->withRedirect(
                $this->router->pathFor('list', ['set' => $params['set']]),
                200
            );
        }

        if ($params['set'] == 'code_set') {
            $admin = [];
            foreach ($params['admin'] as $value) {
                if ($value) {
                    $admin[$value] = 1;
                }
            }
            $e->adminPut($params['code'], $admin);
        }

        if ($params['code_old'] != '' && $params['code_old'] != $params['code']) {
            $e->rename($params['set'], $params['code_old'], $params['code']);
        }

        // Make checkbox values to integer, also if not set
        $params['quantity'] = +!!isset($params['quantity']);
        $params['var']      = +!!isset($params['var']);
        $params['active']   = 1 - !!isset($params['active']);

        if ($params['code'] != '') {
            if (empty($params['desc'])) {
                // Add new key
                $params['desc'] = [ $e->native => $params['code'] ];
            }

            // dbg($params);

            foreach ($params['desc'] as $lang => $desc) {
                $e->putData(
                    $params['set'],
                    $lang,
                    $params['code'],
                    $desc,
                    $params['quantity'],
                    $params['var'],
                    $params['order'],
                    $params['active'],
                    $params['hint'][$lang]
                );
            }

            // exit;

            $this['flash']('CodeSaved');
        }

        if (array_key_exists('go-next', $params)) {
            // Decide where to go next
            if (!empty($params['next'])) {
                // Edit next code
                return $response->withRedirect($params['next'] . '#edit');
            } else {
                // Add next code, no languages here!
                return $response->withRedirect(
                    $this->router->pathFor('edit', ['set' => $params['set']]) . '#edit'
                );
            }
        }

        // Return to list view by default
        return $response->withRedirect(
            $this->router->pathFor(
                'list',
                [ 'set' => $params['set'], 'lang' => $params['lang'], 'lang2' => $params['lang2'] ]
            )
        );
    }
)->setName('POST edit');
