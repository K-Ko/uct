<?php
/**
 *
 */
namespace App;

/**
 *
 */
use App\Extension;
use Slim\App;
use Slim\Container;

/**
 * Extension class
 */
class Extensions
{

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    public function register(App $app, Extension $extension)
    {
        $keys = $extension->register($app);

        foreach ($keys as $key) {
            if (!is_array($key)) {
                $key = [ $key, 0 ];
            }

            @list($key, $pos) = $key;

            $key = strtolower($key);

            if (!isset($this->extensions[$key])) {
                $this->extensions[$key] = [];
            }

            // Don't change given position, calculate for each key separate
            $p = +$pos;

            while (isset($this->extensions[$key][$p])) {
                $p++;
            }

            $this->extensions[$key][$p] = $extension;
            ksort($this->extensions[$key]);
        }
    }


    public function process($key, &$args)
    {

        if (!isset($this->extensions[$key])) {
            return;
        }

        foreach ($this->extensions[$key] as $extension) {
            $ok = $extension->process($key, $msg, $args);
            // $ok = call_user_func_array([ $extension, 'process' ], $data);

            // 1st check for error message and throw as Exception
            if ($msg != '') {
                throw new Exception($msg);
            }

            // 2nd check to skip processing
            if ($ok === false) {
                break;
            }
        }
    }


    public function content($key)
    {
        $content = '';

        $key = strtolower($key);

        if (!empty($this->extensions[$key])) {
            // At least one extension for this key is defined
            $content .= PHP_EOL . '<!-- ' . $key . ' >>> -->' . PHP_EOL;

            foreach ($this->extensions[$key] as $extension) {
                $content .= trim($extension->content($key)) . PHP_EOL;
            }

            $content .= '<!-- <<< ' . $key . ' -->' . PHP_EOL;
        }

        // Render content
        return $this->container['view']->fetchFromString($content);
    }


    protected $container;

    protected $extensions = [];
}
