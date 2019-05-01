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
$app->add(function (Request $request, Response $response, $next) {
    if ($language = $request->getQueryParam('language')) {
        Session::set('language', $language);
        Session::setCookie('language', $language, 365 * 24 * 60 * 60);
        // return $response->withRedirect($this->request->getUri()->getPath(), 200);
    }

    if ($route = $request->getAttribute('route')) {
        if ($route->getName() != 'login' &&
            !(Session::get(Session::LOGIN))) {
            Session::logout();
            return $response->withRedirect($this->router->pathFor('login'));
        }

        $args = $route->getArguments();

        if (isset($args['lang'], $args['lang2']) && $args['lang'] == $args['lang2']) {
            unset($args['lang2']);
        }
    } else {
        $args = [];
    }

    // return NotFound for non existent route
    // if (empty($route)) {
    //     return $next($request, $response);
    // }

    $cfg  = $this['config'];
    $view = $this['view'];

    $view['native'] = $this['editor']->native;

    $language = Session::get('language');

    $adminNav = $exclude = [];

    // Exclude all system code sets
    foreach ($this['editor']->fullSetAssoc('code_set', $language) as $set => $data) {
        if ($this['editor']->isSystemSet($data['code'])) {
            // $adminNav[$set] = $data['desc'];
            if ($set != 'code_admin') {
                $adminNav[$set] = [
                    'url' => $this->router->pathFor('list', [ 'set' => $set ]),
                    'name' => $this['i18n']->h('code_set::' . $set)
                ];
            }
            $exclude[] = $set;
        }
    }

    $view['IndexCodeSetSelect'] = $this['editor']->selectOptions(
        'code_set',
        $language,
        [
            'select_prompt' => $this->i18n->CodeSet . ' ?',
            'blank_prompt'  => $this->i18n->AllCodes,
            'value'         => isset($args['set']) ? $args['set'] : '',
            'exclude'       => $exclude
        ]
    );

    $view['IndexLanguageSelect'] = $this['editor']->selectOptions(
        'code_lang',
        $language,
        [
            // No prompt and no empty, defaults to native
            'value' => isset($args['lang']) ? $args['lang'] : $language
        ]
    );

    $view['IndexLanguage2Select'] = $this['editor']->selectOptions(
        'code_lang',
        $language,
        [
            'select_prompt' => $this['editor']->data('code_set', $language, 'code_lang') . ' 2 ?',
            'blank_prompt'  => '(' . $this->i18n->none . ')',
            'value'         => isset($args['lang2']) ? $args['lang2'] : ''
        ]
    );

    $view['IndexDisplayLanguageSelect'] = $this['editor']->selectOptions(
        'code_lang',
        $language,
        [ 'value' => $language ]
    );

    $view['user'] = $this['user'];
    $view['app'] = $this['app'];
    $view['can'] = $this['can'];

    $view['text_length']  = isset($cfg['TEXTLENGTH']) ? $cfg['TEXTLENGTH'] : PHP_INT_MAX;
    $view['version']      = $cfg['version'];
    $view['version_date'] = $cfg['version_date'];
    $view['languages']    = $this['editor']->languageSet('code_lang', $language);
    $view['lang_count']   = $this['editor']->activeLanguages();

    $view['route'] = '';
    $view['extensions'] = $this['extensions'];

    try {
        $this['extensions']->process('nav-code-sets', $adminNav);
    } catch (Exception $e) {
        $this['flash']->addMessage('danger', $e->getMessage());
    }

    $view['adminNav'] = array_reverse($adminNav);

    $response = $next($request, $response);

    // https://juriansluiman.nl/article/156/app-notfound-for-slim-v3
    // Check if the response should render a 404
    if (404 === $response->getStatusCode() && 0 === $response->getBody()->getSize()) {
        // A 404 should be invoked
        return $this['notFoundHandler']($request, $response);
    }

    if ($this['develop'] && stristr($response->getHeader('Content-Type')[0], 'text/html') && !$request->isXhr()) {
        $dumper = new Twig_Profiler_Dumper_Text();
        $response->getBody()->write(
            PHP_EOL . PHP_EOL .
            '<!-- ' . str_repeat('- ', 35) . PHP_EOL . PHP_EOL .
            $dumper->dump($this['profiler']) .
            // print_r($this['db']->queries .
            PHP_EOL . str_repeat('- ', 35) . ' -->' . PHP_EOL . PHP_EOL
        );
    }

    $log = $this['baseDir'] . '/log/sql.log';

    if ($fh = fopen($log, 'a')) {
        fwrite($fh, str_repeat('-', 78) . PHP_EOL . PHP_EOL);

        foreach ($this['db']->queries as $log) {
            // fwrite($fh, print_r($log, 1) . PHP_EOL);

            $ts = \DateTime::createFromFormat('U.u', $log['ts']);
            $ts = '[ ' . $ts->format('H:i:s.v') . ' ] ';
            $line = preg_replace('~\s+~s', ' ', $log['query']);
            fwrite($fh, $ts . $line . PHP_EOL);

            $line = $log['index'] ? $log['index'] . ' - ' : '';
            fwrite($fh, $ts . $line . $log['result'] . PHP_EOL . PHP_EOL);
        }

        fclose($fh);
    } else {
        echo "The file '$log' is not writable!";
    }

    return $response;
});
