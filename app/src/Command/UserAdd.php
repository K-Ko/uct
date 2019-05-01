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
class UserAdd extends Base
{
    /**
     *
     */
    protected $title = 'Add user';
    protected $help = 'Save user and hashed password into code set "code_user"';

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('user:add')
            ->setDescription($this->title)
            ->setHelp($this->help)
            ->addArgument('user', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->title);

        $user = $input->getArgument('user');

        $io->text("Store <info>$user</info> ...");

        $sql = $this->sql(
            'REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`)
              VALUES ("code_user", "{{NATIVE}}", "%s", "%s")',
            $user,
            sha1($input->getArgument('password'))
        );

        if ($this->query($input, $output, $sql)) {
            $io->success('User credentials saved');
        } else {
            $io->error('Something went wrong');
        }
    }
}
