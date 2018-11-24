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
        ->addArgument('native', InputArgument::REQUIRED, 'Primary language, mostly "en"; one of (en|de|fr)');
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $native = strtolower($input->getArgument('native'));

        if (!in_array($native, $this->languages)) {
            $io = new SymfonyStyle($input, $output);
            $io->title($this->title);
            $io->error(
                'Invalid language: ' . $native .
                ' - Must be one of (' . implode('|', $this->languages) . ')!'
            );
            exit;
        }

        $command = $this->getApplication()->find('sql:load');

        // Bootstrap
        foreach (glob('sql/bootstrap/*.sql') as $file) {
            $arguments = [
                'command' => 'sql:load',
                'file'    => $file,
                'native'  => $native
            ];

            $command->run(new ArrayInput($arguments), $output);
        }
    }
}
