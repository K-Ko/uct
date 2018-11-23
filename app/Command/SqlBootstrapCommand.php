<?php
/**
 *
 */
namespace Command;

/**
 *
 */
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 *
 */
class SqlBootstrapCommand extends BaseCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
        ->setName('sql:bootstrap')
        ->setDescription('Create database table and fill basic data');
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('sql:load');

        $arguments = [
            'command' => 'sql:load',
            'file'    => $this->baseDir.'/sql/bootstrap.sql'
        ];

        $initInput = new ArrayInput($arguments);
        $command->run($initInput, $output);
    }
}
