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
class SqlLoad extends Base
{
    /**
     *
     */
    protected $title = 'Load SQL file';
    protected $help = 'Load SQL file into database';

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('sql:load')
            ->setDescription($this->title)
            ->setHelp($this->help)
            ->addArgument('file', InputArgument::REQUIRED, 'SQL File to load')
            ->addArgument('language', InputArgument::OPTIONAL);
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

        $db = $this->connect($input, $output);

        $io->text('Process ' . $file . ' ...');

        $native = $input->getArgument('language');

        if ($native == '') {
            $native = self::$native;
        }

        if ($native == '') {
            die($io->error('Something went wrong, can\'t find native language!'));
        }

        $sql = file_get_contents($file);

        $sql = str_replace('{{TABLE}}', self::$table, $sql);
        $sql = str_replace('{{NATIVE}}', $native, $sql);

        $count = 0;

        // $io->text($sql);

        if ($db->multi_query($sql)) {
            while ($db->more_results() && $db->next_result()) {
                $count++;
            }

            if ($db->errno) {
                $io->error($db->error);
                die($io->text($sql));
            }

            $io->success("Successful processed $count instructions");
        } else {
            die($io->error($db->error));
        }
    }
}
