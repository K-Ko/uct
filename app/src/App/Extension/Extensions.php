<?php
/**
 *
 */
namespace App\Extension;

/**
 * Definitions for Password self-service extension
 */
use App\Extension;
use App\Session;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class Extensions extends Extension
{

    public function register(App $app)
    {
        if ($this->container['app']['id'] != 0) {
            return [];
        }

        $app->get('/extensions', function (Request $request, Response $response) {

            $enabled = require $this['baseDir'] . '/app/extensions.php';
            $rows = [];

            foreach (glob($this['baseDir'] . '/app/src/App/Extension/*.php') as $file) {
                $extension = substr(basename($file), 0, -4); // Remove '.php';

                if ($extension == 'Extension' || $extension == 'Extensions') {
                    // Skip this Extensions.php
                    continue;
                }

                $class = 'App\Extension\\' . $extension;

                $rows['0'.$class::$name] = [
                    'class' => $extension,
                    'name' => $class::$name,
                    'description' => $class::$description,
                    'version' => $class::$version,
                    'link' => $class::$link,
                    'custom' => false,
                    'date' => !empty($enabled[$extension]) ? $enabled[$extension] : ''
                ];
            }

            foreach (glob($this['baseDir'] . '/custom/Extension/*.php') as $file) {
                $extension = substr(basename($file), 0, -4); // Remove '.php';

                $class = 'App\Extension\\' . $extension;

                $rows['1'.$class::$name] = [
                    'class' => $extension,
                    'name' => $class::$name,
                    'description' => $class::$description,
                    'version' => $class::$version,
                    'link' => $class::$link,
                    'custom' => true,
                    'date' => !empty($enabled[$extension]) ? $enabled[$extension] : ''
                ];
            }

            ksort($rows);

            $this['view']->render($response, 'Extensions/list.twig', [ 'rows' => array_values($rows) ]);
            return $response;
        })->setName('Extensions list');

        $app->post('/extensions/toggle/{name}', function (Request $request, Response $response, $name) {

            $extFile = $this['baseDir'] . '/app/extensions.php';
            $enabled = require $extFile;

            $class = 'App\Extension\\' . $name;

            try {
                $class = new $class($this);

                if (!isset($enabled[$name])) {
                    // Install translations if needed
                    $class->enable();

                    $enabled[$name] = date('Y-m-d');

                    $this['flash']('code_ext::extensions_Enabled');
                } else {
                    // Uninstall translations if needed
                    $class->disable();

                    unset($enabled[$name]);

                    $this['flash']('code_ext::extensions_Disabled');
                }

                file_put_contents($extFile, '<?php return ' . var_export($enabled, true) . ';', LOCK_EX);
                // Trick the file cache :-(
                sleep(3);
            } catch (Exception $e) {
                $this['flash']($e->getMessage(), 'danger');
            }

            return $response->withRedirect($this->router->pathFor('Extensions list'), 200);
        })->setName('Extensions toggle');

        // Register as last menu entry
        return [ [ 'nav-admin-after', 100 ] ];
    }
}
