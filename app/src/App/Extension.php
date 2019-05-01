<?php
/**
 *
 */
namespace App;

/**
 *
 */
use RuntimeException;
use Slim\App;
use Slim\Container;

/**
 * Extension class
 */
abstract class Extension
{
    /**
     * Extension descriptions
     *
     * @var string
     */
    public static $name = '';
    public static $description = '';
    public static $version = '';
    public static $link = '';

    /**
     * Register an extension
     *
     * @param \Slim\App $app
     * @return array
     */
    abstract public function register(App $app);

    /**
     * Class constructor
     *
     * @param \Slim\Container $c
     */
    public function __construct(Container $c)
    {
        $this->container = $c;

        $this->path = [
            // System extensions
            $c['baseDir'] . '/app/src/App/Extension',
            // Custom extensions
            $c['baseDir'] . '/custom/Extension'
        ];

        $class = explode('\\', get_class($this));
        $this->class = array_pop($class);
    }

    /**
     * Process extension point
     *
     * @param string $name Extension point
     * @param string $msg Error message if needed, break procession with Exception
     * @param array $args Optional arguments
     * @return boolean Break further extesion on FALSE
     */
    public function process($name, &$msg, Array &$args)
    {
        // Does nothing be default, overwrite if needed
    }

    /**
     * Get content for template extension point
     *
     * @param string $key
     * @return string
     */
    final public function content($key)
    {
        // 1st search matching Twig template
        $tpl = $this->class . '/' . $key . '.twig';

        foreach ($this->path as $path) {
            $file = $path . '/' . $tpl;
            if (is_file($file)) {
                return file_get_contents($file);
            }
        }

        // 2nd check for user defined content<Key>() method
        // e.g. "nav-admin-before" search for "contentNavAdminBefore"
        $method = 'content' . str_replace(' ', '', ucwords(strtolower(str_replace('-', ' ', $key))));

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        // If nothing was found, throw an error
        throw new RuntimeException(
            'Missing file "' . $tpl . '" or method "' . $this->class . '->' . $method. '()"!'
        );
    }

    /**
     * Enable extension
     *
     * @return void
     */
    public function enable()
    {
        $this->loadSql('install.sql');
        // Update composer class map
        exec('cd ' . $this->container['baseDir'] . ' && composer dump -qa');
    }

    /**
     * Disable extension
     *
     * @return void
     */
    public function disable()
    {
        $this->loadSql('uninstall.sql');
    }

    /**
     * App container
     *
     * @var \Slim\Container
     */
    protected $container;

    /**
     * Base paths for extensions
     *
     * @var array
     */
    protected $path;

    /**
     * Actual class name
     *
     * @var string
     */
    protected $class;

    /**
     * Load SQL file if exists from enable(), disable()
     *
     * @throws \RuntimeException In case of script error
     * @param string $file
     * @return integer
     */
    protected function loadSql($file)
    {
        $db = $this->container['db'];
        $rc = 0;

        foreach ($this->path as $path) {
            $file = $path . '/' . $this->class . '/' . $file;

            if (is_file($file)) {
                $sql = file_get_contents($file);

                $sql = $this->container['editor']->sql($sql);

                if ($db->multi_query($sql)) {
                    do {
                        $db->store_result();
                        $rc++;
                    } while ($db->more_results() && $db->next_result());

                    if ($db->errno) {
                        throw new RuntimeException($this->error, $this->errno);
                    }
                }

                break;
            }
        }

        return $rc;
    }
}
