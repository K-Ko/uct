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
    '/list/{set}[/{lang}[/{lang2}]]',
    function (Request $request, Response $response, array $args) {
        $data = array_merge(array('lang' => $this['editor']->native, 'lang2' => ''), $args);

        if ($data['lang'] != $this['editor']->native && $data['lang2'] == '') {
            $data['lang']  = $this['editor']->native;
            $data['lang2'] = $data['lang'];
        }

        $data['admin'] = $this['editor']->adminGet($data['set']);

        if ($data['admin']['param'] || $data['lang'] == $data['lang2']) {
            $data['lang2'] = null;
        }

        $base_set = $this['editor']->languageSet($data['set'], $data['lang']);
        $data['rec_count'] = count($base_set);

        if ($data['set'] == 'code_set') {
            // Remove code_admin from list
            $base_set = array_filter($base_set, function ($row) {
                return $row['code'] !== 'code_admin';
            });
        }

        if ($data['lang2']) {
            // Add the second language descriptions.
            $lang_lookup = array();
            foreach ($this['editor']->languageSet($data['set'], $data['lang2']) as $row) {
                $lang_lookup[$row['code']] = $row['desc'];
            }

            foreach ($base_set as $n => $row) {
                $base_set[$n]['desc2'] = $_=&$lang_lookup[$row['code']] ?: '';
            }
            unset($lang_lookup);
        }

        $data['set_desc']  = $this['editor']->desc('code_set', null, $data['set']);

        $data['set_rows'] = [];
        foreach ($base_set as $row) {
            if (Session::loggedIn() || $row['order'] >= 0) {
                $data['set_rows'][] = array_merge($row, $this['editor']->adminGet($row['code']));
            }
        }

        return $this->view->render($response, 'list.twig', $data);
    }
)->setName('list');
