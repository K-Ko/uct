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
class BaseCommand extends Command
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->baseDir = realpath(__DIR__.'/../../');
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
    protected $db;

    /**
     *
     */
    protected function connect(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->db = @new MySQLi(
            getenv('DBHOST'),
            getenv('DBUSER'),
            getenv('DBPASS'),
            getenv('DBNAME')
        );

        if ($this->db->connect_error) {
            die($io->error($this->db->connect_error));
        }

        $this->db->set_charset('utf8');

        $io->success('Connected to database');
    }
}
