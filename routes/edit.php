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
    '/edit/{set}[/{code}[/{lang}[/{lang2}]]]',
    function (Request $request, Response $response, array $args) {
        $native = $this['editor']->native;

        $data = array_merge(
            [
                'code'     => '',
                'lang'     => '',
                'lang2'    => '',
                'nav'      => null,
                'quantity' => 0,
                'order'    => 0,
                'active'   => 1,
                'hint'     => ''
            ],
            $args,
            $this['editor']->adminGet($args['set'])
        );

        $data['admin'] = $data['set'] == 'code_set' ? $this['editor']->adminGet($data['code']) : null;

        if ($data['code'] == '') {
            // Add new code, No navigation!
            $data['nav'] = ['count' => 0];
        } else {
            $data['nav'] = $this['editor']->getNav($data['set'], $data['code']);
            if (!$data['nav']) {
                Session::flash('InvalidCode|danger');
                $path = $this->router->pathFor('list', ['set' => $data['set']]);
                return $response->withRedirect($path, 200);
            }
        }

        $code = $this['editor']->get($data['set'], $native, $data['code']);

        if (!empty($code)) {
            $data = array_merge($data, $code);
            $data['hint'] = $this['editor']->getHint($data['set'], $data['code']);
        }

        $data['code_admin'] = $this['editor']->adminGet($data['set']);

        if ($data['code_admin']['param']) {
            // Parameter set, only native language
            $data['lang_rows'] = [
                [
                    'code'   => $native,
                    'desc'   => $this['editor']->desc('code_lang', Session::get('language'), $native),
                    'order'  => 0,
                    'active' => 1
                ]
            ];
        } elseif ($data['lang2'] == '') {
            // All languages
            $data['lang_rows'] = $this['editor']->languageSet('code_lang', Session::get('language'));
        } else {
            // 2 languages only
            $data['lang_rows'] = [
                [
                    'code'   => $data['lang'],
                    'desc'   => $this['editor']->desc('code_lang', Session::get('language'), $data['lang']),
                    'order'  => 0,
                    'active' => 1
                ],
                [
                    'code'  => $data['lang2'],
                    'desc'  => $this['editor']->desc('code_lang', Session::get('language'), $data['lang2']),
                    'order' => 0,
                    'active' => 1
                ]
            ];
        }

        foreach ($data['lang_rows'] as $id => &$row) {
            if (!$row['active']) {
                unset($data['lang_rows'][$id]);
                continue;
            }

            $row['desc'] = ucfirst($row['desc']);
            $row['code_desc'] = $this['editor']->data($data['set'], $row['code'], $data['code']);
            $nat_desc = urlencode($this['editor']->data($data['set'], $native, $data['code']));

            if ($row['code'] == $native) {
                $row['translate'] = null;
            } else {
                $row['translate'][0] = sprintf(
                    'https://translate.google.com/#%s/%s/%s',
                    substr($native, 0, 2),
                    substr($row['code'], 0, 2),
                    $nat_desc
                );
                $row['translate'][1] = sprintf(
                    'https://www.deepl.com/translator#%s/%s/%s',
                    substr($native, 0, 2),
                    substr($row['code'], 0, 2),
                    $nat_desc
                );
            }
        }

        $data['desc'] = $this['editor']->desc('code_set', null, $data['set']);

        return $this->view->render($response, 'edit.html', $data);
    }
)->setName('edit');

/**
 *
 */
$app->get(
    '/edit',
    function (Request $request, Response $response, array $args) {
        return $response->withRedirect($this->router->pathFor('home'), 200);
    }
);

/**
 *
 */
$app->post(
    '/edit',
    function (Request $request, Response $response, array $args) {
        $params = $request->getParsedBody();

        array_walk($params, function (&$p) {
            is_scalar($p) && $p = trim($p);
        });

        // Make checkbox value to integer
        $params['quantity'] = +!!isset($params['quantity']);

        if (!preg_match('~^[a-zA-Z0-9_]+$~', $params['code'])) {
            Session::flash('CodeRegexFailed|danger');
            return $response->withRedirect(
                $this->router->pathFor('list', ['set' => $params['set']]),
                200
            );
        }

        if (!preg_match('~^-?[0-9]+$~', $params['order'])) {
            Session::flash('CodeOrderNotNumeric|danger');
            return $response->withRedirect(
                $this->router->pathFor('list', ['set' => $params['set']]),
                200
            );
        }

        // Extra work for deprecated flag
        if (isset($params['admin']) && in_array('active', $params['admin'])) {
            $active = 0;
            unset($params['admin']['active']);
        } else {
            $active = 1;
        }

        if ($params['set'] == 'code_set') {
            $admin = [];
            foreach ($params['admin'] as $value) {
                if ($value) {
                    $admin[$value] = 1;
                }
            }
            $this['editor']->adminPut($params['code'], $admin);
        }

        if ($params['code'] != '') {
            if (empty($params['desc'])) {
                // Add new key
                $params['desc'] = [ $this['editor']->native => $params['code'] ];
            }

            foreach ($params['desc'] as $lang => $desc) {
                $this['editor']->putData(
                    $params['set'],
                    $lang,
                    $params['code'],
                    $desc,
                    $params['quantity'],
                    $params['order'],
                    $active
                );
            }

            if (array_key_exists('hint', $params)) {
                $this['editor']->setHint($params['set'], $params['code'], $params['hint']);
            }

            Session::flash('CodeSaved');
        }

        if (!empty($params['next'])) {
            // Edit next code
            return $response->withRedirect($params['next'].'#edit', null, 200);
        } else {
            // Add next code
            return $response->withRedirect(
                $this->router->pathFor('edit', ['set' => $params['set']]),
                200
            );
        }
    }
)->setName('POST edit');
