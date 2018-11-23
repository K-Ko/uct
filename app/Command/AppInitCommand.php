<?php
/**
 *
 */
namespace Command;

/**
 *
 */
use Command\Base;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;

/**
 * List SQL files
 */
class AppInitCommand extends BaseCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
        ->setName('app:init')
        ->setDescription('Init application config');
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Environment settings');

        $envFile = $this->baseDir . '/.env';

        if (file_exists($envFile)) {
            $io->error($envFile . ' file exists!');
            exit;
        }

        $env = file_get_contents($this->baseDir . '/.env.dist');

        $questions = [
            // question,            default,     var
            [ 'Database host',     'localhost', 'DBHOST'  ],
            [ 'Database user',     'root',      'DBUSER'  ],
            [ 'Database password', '',          'DBPASS'  ],
            [ 'Database',          'uct',       'DBNAME'  ],
            [ 'Database table',    'uct',       'DBTABLE' ],
            [ 'Administration password', 'uct', 'LOGIN'   ]
        ];

        $helper = $this->getHelper('question');

        foreach ($questions as &$q) {
            $msg = vsprintf('%s [%s]', $q);
            $msg .= str_repeat(' ', 30 - strlen($msg)) . ' : ';
            $ans = $helper->ask($input, $output, new Question($msg, $q[1]));
            if ($q[2] == 'LOGIN') {
                $ans = sha1($ans);
            }
            $env = preg_replace('~^('.$q[2].'=).*~m',  "\$1'$ans'", $env);
        }

        if (file_put_contents($envFile, $env)) {
            $io->writeln('');
            $io->success('Saved ' . $envFile);
            $io->text('Adjust further settings for your needs.');
        } else {
            $io->error('can\'t save ' . $envFile);
        }

    }
}
