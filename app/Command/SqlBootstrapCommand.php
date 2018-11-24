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
use Symfony\Component\Console\Input\InputArgument;
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
    protected $title = 'Create database table and fill basic data';

    /**
     * Available languages
     *
     * @var array
     */
    protected $languages = ['en', 'de', 'fr'];

    /**
     *
     */
    protected function configure()
    {
        $this
        ->setName('sql:bootstrap')
        ->setDescription($this->title)
        ->addArgument('language', InputArgument::REQUIRED, 'Primary language, mostly "en"; one of (en|de|fr)');
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $language = strtolower($input->getArgument('language'));

        if (!in_array($language, $this->languages)) {
            $io = new SymfonyStyle($input, $output);
            $io->title($this->title);
            $io->error(
                'Invalid language: ' . $language .
                ' - Must be one of (' . implode('|', $this->languages) . ')!'
            );
            exit;
        }

        $command = $this->getApplication()->find('sql:load');

        $arguments = [
            'command'  => 'sql:load',
            'file'     => $this->baseDir.'/sql/bootstrap.sql',
            'language' => $language
        ];

        $initInput = new ArrayInput($arguments);
        $command->run($initInput, $output);
    }
}
