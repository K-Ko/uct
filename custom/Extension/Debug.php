<?php
/**
 *
 */
namespace App\Extension;

/**
 * Definitions for User admin
 */
use App\Extension;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class Debug extends Extension
{

    public static $name = 'Debugger';
    public static $description = 'Debugger extension';
    public static $version = '1.0.0';
    public static $link = '';


    public function register(App $app)
    {
        // Register extension point(s) as last inside container
        return $this->container['develop']
             ? [ 'nav-center', [ 'page-after', 999 ] ]
             : [];
    }

    // For template extension "scripts"
    public function contentPageAfter()
    {
        return '<div class="row"><div class="col">'
             . $this->dbgSession()
             . $this->dbgGetMySQL()
             . '</div></div>';
    }


    protected function dbgHeader($header)
    {
        return '<div class="border-top h5 mt-3 py-3">' . $header . '</div>';
    }


    protected function dbgGetMySQL()
    {
        $content = $this->dbgHeader('MySQL queries')
                 . '<table class="table table-striped text-monospace">'
                 . '<thead>'
                 . '<tr>'
                 . '<th>Query</th>'
                 . '<th>Index</th>'
                 . '<th>Result</th>'
                 . '</tr>'
                 . '</thead>';

        foreach ($this->container['db']->queries as $query) {
            $content .= '<tr>'
                      . '<td>' . $query['query'] . '</td>'
                      . '<td>' . $query['index'] . '</td>'
                      . '<td>' . $query['result'] . '</td>'
                      . '</tr>';
            // $content .= '<tr><td>' . print_r($query) . '</td></tr>';
        }

        return $content . '</table>';
    }


    protected function dbgSession()
    {
        return $this->dbgHeader('$_SESSION')
             . '<div class="text-monospace">' . print_r($_SESSION, 1) . '</div>';
    }
}
