<?php
/**
 *
 */
namespace Command;

/**
 *
 */
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 *
 */
class SqlLoadCommand extends BaseCommand
{
    /**
     *
     */
    protected $title = 'Load SQL file';

    /**
     *
     */
    protected function configure()
    {
        $this
        ->setName('sql:load')
        ->setDescription($this->title)
        ->setHelp('Load SQL file into database.')
        ->addArgument('file', InputArgument::REQUIRED, 'SQL File to load');
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->title);

        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $io->error('Invalid file: '.$file);
            exit;
        }

        $this->connect($input, $output);

        $io->text('Process <info>'.$file. '</info> ...');

        $sql = str_replace('{{TABLE}}', getenv('DBTABLE'), file_get_contents($file));

        if ($this->db->multi_query($sql)) {
            while ($this->db->next_result()) {
                ; // Just flush
            }
            $io->success('Successful imported');
        } else {
            $io->error($this->db->error);
        }
    }
}
