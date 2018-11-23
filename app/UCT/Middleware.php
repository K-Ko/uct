<?php
/**
 *
 */
namespace UCT;

/**
 *
 */
use Slim\Http\Request;
use Slim\Http\Response;
use Core\Session;

/**
 *
 */
class Middleware
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $route = $request->getAttribute('route');

        // return NotFound for non existent route
        if (empty($route)) {
            return $next($request, $response);
        }

        $c = $this->container;

        $queryParams = $request->getQueryParams();

        if (array_key_exists('language', $queryParams)) {
            Session::set('language', $queryParams['language']);
            Session::setCookie('language', $queryParams['language'], 365 * 24 * 60 * 60);
            return $response->withRedirect($c->request->getUri()->getPath(), 200);
        }

        $cfg  = $c->config;
        $view = $c->view;
        $args = $route->getArguments();

        if (isset($args['lang'], $args['lang2']) && $args['lang'] == $args['lang2']) {
            unset($args['lang2']);
        }

        $view['native'] = $c['editor']->native;
        $view['title']  = isset($cfg['TITLE']) ? $cfg['TITLE'] : 'Universal Code Translation';

        if (!empty($flash = Session::flash())) {
            if (strpos($flash[0], '|')) {
                $flash = explode('|', $flash[0]);
                $view['flash'] = ['message' => $flash[0], 'type' => $flash[1]];
            } else {
                $view['flash'] = ['message' => $flash[0]];
            }
        }

        $loggedIn = Session::loggedIn();
        $language = Session::get('language');

        $view['IndexCodeSetSelect'] = $c['editor']->selectOptions(
            'code_set',
            $language,
            [
                'select_prompt' => $c->i18n->CodeSet . ' ?',
                'blank_prompt'  => $c->i18n->AllCodes,
                'subset'        => $c->config['CODESETS'],
                'value'         => $loggedIn && isset($args['set']) ? $args['set'] : '',
                'exclude'       => $loggedIn ? [] : ['code_set', 'code_lang', 'code_editor'],
            ]
        );

        $view['IndexLanguageSelect'] = $c['editor']->selectOptions(
            'code_lang',
            $language,
            [
                'select_prompt' => $c['editor']->data('code_set', $language, 'code_lang') . ' ?',
                'blank_prompt'  => '(' . $c->i18n->none . ')',
                'value'         => isset($args['lang']) ? $args['lang'] : ''
            ]
        );

        $view['IndexLanguage2Select'] = $c['editor']->selectOptions(
            'code_lang',
            $language,
            [
                'select_prompt' => $c['editor']->data('code_set', $language, 'code_lang') . ' 2 ?',
                'blank_prompt'  => '(' . $c->i18n->none . ')',
                'value'         => isset($args['lang2']) ? $args['lang2'] : ''
            ]
        );

        $view['IndexDisplayLanguageSelect'] = $c['editor']->selectOptions(
            'code_lang',
            $language,
            [ 'value' => $language ]
        );

        $view['logged_in']    = $loggedIn;
        $view['can_add']      = $loggedIn;
        $view['can_edit']     = true;
        $view['can_delete']   = $loggedIn;
        $view['text_length']  = isset($cfg['TEXTLENGTH']) ? $cfg['TEXTLENGTH'] : PHP_INT_MAX;
        $view['layout']       = $cfg['LAYOUT'];
        $view['version']      = $cfg['VERSION'];
        $view['version_date'] = $cfg['VERSION_DATE'];
        $view['languages']    = $c['editor']->languageSet('code_lang', $language);
        $view['lang_count']   = $c['editor']->activeLanguages();

        $response = $next($request, $response);

        return $response;
    }
}
