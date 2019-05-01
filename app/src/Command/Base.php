<?php
/**
 *
 */
namespace Command;

/**
 *
 */
use MySQLi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 *
 */
class Base extends Command
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->baseDir = realpath(__DIR__.'/../../..');
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $baseDir;

    /**
     *
     */
    protected $config;

    /**
     * Database table
     *
     * @var string
     */
    protected static $table;

    /**
     * Native language
     *
     * @var string
     */
    protected static $native;


    /**
     * Singleton MySQLi instance
     *
     * @var \MySQLi
     */
    private static $db = false;

    /**
     * Build SQL command with placeholders
     *
     * @param string $sql
     * @return string
     */
    protected function sql($sql)
    {
        $args = func_get_args();
        $sql = array_shift($args);
        $sql = vsprintf($sql, $args);

        return $sql;
    }

    /**
     * MySQLi query wrapper
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $sql
     * @return mixed
     */
    protected function query(InputInterface $input, OutputInterface $output, $sql)
    {
        $args   = func_get_args();
        $input  = array_shift($args);
        $output = array_shift($args);
        $sql    = trim(array_shift($args));

        $this->connect($input, $output);

        // Replace commons AFTER connect()
        $sql = str_replace('{{TABLE}}', self::$table, $sql);
        $sql = str_replace('{{NATIVE}}', self::$native, $sql);

        $res = self::$db->query($sql);

        if (self::$db->errno) {
            $io = new SymfonyStyle($input, $output);
            $io->error(self::$db->error);
            die($io->text(preg_replace('~\s+~s', ' ', $sql)));
        }

        return $res;
    }

    /**
     *
     */
    protected function connect(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->config = require $this->baseDir . '/config.local.php';

        $c = $this->config['database'];

        self::$db = @new MySQLi($c['host'], $c['user'], $c['pass'], $c['name']);

        if (self::$db->connect_error) {
            die($io->error(self::$db->connect_error));
        }

        self::$db->set_charset('utf8');

        if (self::$firstConnect) {
            $io->success('Connected to database "' . $c['user'] . '@' . $c['name'] . '"');
            self::$firstConnect = false;
        }

        self::$table = $c['table'];

        // Find native in database
        $sql = sprintf(
            'SELECT `lang` FROM `%s`
              WHERE `app` = 0 AND `set` = "code_admin" AND `code` = "code_admin"',
            self::$table
        );

        if ($res = self::$db->query($sql)) {
            self::$native = $res->fetch_row()[0];
        }

        return self::$db;
    }

    /**
     * Alert success only on 1st connect
     *
     * @var boolean
     */
    private static $firstConnect = true;
}
