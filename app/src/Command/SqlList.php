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
 * List SQL files
 */
class SqlList extends Base
{
    /**
     *
     */
    protected function configure()
    {
        $this
        ->setName('sql:list')
        ->setDescription('List available SQL files');
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Available SQL files');

        $io->listing(
            array_map(function ($f) {
                return 'sql/' . basename($f);
            }, glob($this->baseDir.'/sql/*.sql'))
        );
    }
}
