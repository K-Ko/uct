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
    function (Request $request, Response $response, $set, $lang = '', $lang2 = '') {
        $this['view']['route'] = 'list';

        if ($lang == '') {
            $lang = $this['language'];
        }

        $data = [
            'set' => $set,
            'lang' => $lang,
            'lang2' => $lang2,
            'admin' => $this['editor']->adminGet($set)
        ];

        if ($data['admin']['param'] || $lang == $lang2) {
            $lang2 = null;
        }

        $data['lang2'] = $lang2;

        $base_set = $this['editor']->languageSet($set, $lang);
        $data['rec_count'] = count($base_set);

        if ($set == 'code_set') {
            // Remove code_admin from list
            $base_set = array_filter($base_set, function ($row) {
                return $row['code'] !== 'code_admin';
            });
        }

        if ($lang2) {
            // Add the second language descriptions.
            $lang_lookup = [];
            foreach ($this['editor']->languageSet($set, $lang2) as $row) {
                $lang_lookup[$row['code']] = $row['desc'];
            }

            foreach ($base_set as $n => $row) {
                $base_set[$n]['desc2'] = $_=&$lang_lookup[$row['code']] ?: '';
            }
            unset($lang_lookup);
        }

        $data['set_desc']  = $this['editor']->desc('code_set', null, $set);

        $data['set_rows'] = [];
        foreach ($base_set as $row) {
            if ($this->view['can']['admin'] || $row['order'] >= 0) {
                $data['set_rows'][] = array_merge($row, $this['editor']->adminGet($row['code']));
            }
        }

        return $this->view->render($response, 'list.twig', $data);
    }
)->setName('list');
