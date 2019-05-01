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
use Symfony\Component\Console\Question\Question;

/**
 * List SQL files
 */
class AppInit extends Base
{
    /**
     *
     */
    protected $title = 'Init application';
    protected $help  = 'Create application config, create database table and fill basic data';

    /**
     * Template for config.local.php
     *
     * @var string
     */
    protected $configDb = "<?php
/**
 * Custom configuration
 */
return [
    /**
     * Database connection settings
     */
    'database' => [
        'host'  => '{{DBHOST}}',
        'user'  => '{{DBUSER}}',
        'pass'  => '{{DBPASS}}',
        'name'  => '{{DBNAME}}',
        'table' => '{{DBTABLE}}',
    ],

    /**
     * More settings goes here
     */

];
";

    /**
     * Local config file
     *
     * @var string
     */
    protected $configLocal;

    /**
     * Class constructor
     *
     * @param Array $languages
     */
    public function __construct(Array $languages)
    {
        $this->languages = $languages;

        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('app:init')
            ->setDescription($this->title)
            ->setHelp($this->help);
    }

    /**
     * Initialize command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->configLocal = $this->baseDir . '/config.local.php';

        if (file_exists($this->configLocal)) {
            $io = new SymfonyStyle($input, $output);
            die($io->error($this->configLocal . ' still file exists!'));
        }

        $file = $this->baseDir . '/app/extensions.php';

        if (!is_file($file)) {
            file_put_contents($file, '<?php return [ \'Extensions\' => true ];');
        }
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Native language');

        $native = $io->choice('Please select', $this->languages);

        $io->title('Configuration');

        if (!in_array($native, $this->languages)) {
            die($io->error(
                'Invalid language: ' . $native .
                ' - Must be one of (' . implode('|', $this->languages) . ')!'
            ));
        }

        $config = $this->configDb;

        $settings = [
            // var         question,            default,
            'DBHOST'  => [ 'Database host',     'localhost' ],
            'DBUSER'  => [ 'Database user',     'root'      ],
            'DBPASS'  => [ 'Database password', ''          ],
            'DBNAME'  => [ 'Database',          'uct'       ],
            'DBTABLE' => [ 'Database table',    'uct'       ]
        ];

        foreach ($settings as $var => &$q) {
            $q = $io->ask($q[0], $q[1]);
            $config = str_replace('{{' . $var . '}}', $q, $config);
        }

        if (file_put_contents($this->configLocal, $config)) {
            // Test database configuration!
            $this->connect($input, $output);

            $io->writeln('');
            $io->success('Saved ' . $this->configLocal);
            $io->note('Adjust further settings for your needs.');

            $sqlLoadCmd = $this->getApplication()->find('sql:load');

            // Use temp. SQL file containing all bootstrap SQLs
            try {
                // ------------
                // foreach (glob('sql/bootstrap/*.sql') as $file) {
                //     $args = [ 'file' => $file, 'language' => $native ];
                //     $sqlLoadCmd->run(new ArrayInput($args), $output);
                // }
                // ------------
                $tmp = sys_get_temp_dir() . '/bootstrap.sql';

                $fh  = fopen($tmp, 'wa');

                foreach (glob('sql/bootstrap/*.sql') as $file) {
                    fwrite($fh, file_get_contents($file) . PHP_EOL);
                }

                fclose($fh);

                // Run sql:load once with $file
                $args = [ 'file' => $tmp, 'language' => $native ];
                $sqlLoadCmd->run(new ArrayInput($args), $output);

                // Remove temp. SQL file
                unlink($tmp);
                // ------------
            } catch (Exception $e) {
                @unlink($tmp);
                die($io->error($e-getMessage()));
            }

            $io->title('Define system admin user');

            $admin = $io->ask('Name', 'admin');

            do {
                $password = $io->askHidden('Password');
                $passtest = $io->askHidden('Repeat password');

                if ($password !== $passtest) {
                    $io->error('Password inputs was not equal');
                }
            } while ($password !== $passtest);

            $args = [ 'user' => $admin, 'password' => $password ];
            $this->getApplication()->find('user:add')->run(new ArrayInput($args), $output);

            $args = [ 'user' => $admin, 'level' => 9 ];
            $this->getApplication()->find('acl:add')->run(new ArrayInput($args), $output);

            // Adjust hints
            $this->query($input, $output, $this->sql(
                'UPDATE `{{TABLE}}`
                    SET `order` = 900,
                        `hint`  = "System admin user MUST NOT be deleted, can be restored only by CLI!"
                  WHERE ( `set` = "code_user" OR `set` = "code_acl" )
                    AND `lang` = "{{NATIVE}}"
                    AND `code` = "%s"',
                $admin
            ));

            // Extra users
            $users = [ 1 => 'reviewer', 'translator', 'developer', 'app_admin' ];

            foreach ($users as $id => $user) {
                $this->query($input, $output, $this->sql(
                    'INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`)
                     VALUES ("code_user", "{{NATIVE}}", "%s", "%s", %d)',
                    $user,
                    sha1($user),
                    $id * 100
                ));

                $this->query($input, $output, $this->sql(
                    'INSERT INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`)
                     VALUES ("code_acl", "{{NATIVE}}", "%s", "%s", %d)',
                    $user,
                    $id,
                    $id * 100
                ));
            }

            $io->success('Created default users: app_admin, developer, translator, reviewer');
            $io->note('Initial passwords are same as user name!');
        } else {
            $io->error('can\'t save ' . $this->configLocal);
        }
    }
}
