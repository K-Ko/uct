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
class AclAdd extends Base
{
    /**
     *
     */
    protected $title = 'Add user access';
    protected $help = 'Save user access rights defintion';

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('acl:add')
            ->setDescription($this->title)
            ->setHelp($this->help)
            ->addArgument('user', InputArgument::REQUIRED, 'Username')
            ->addArgument('level', InputArgument::REQUIRED, 'Access level')
            ->addArgument('set', InputArgument::OPTIONAL, 'Code set');
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->title);

        $user = $input->getArgument('user');

        if ($set = $input->getArgument('set')) {
            $user .= '.' . $set;
        }

        $level = $input->getArgument('level');

        $io->text("Store <info>$user = $level</info> ...");

        $sql = $this->sql(
            'REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`)
              VALUES ("code_acl", "{{NATIVE}}", "%s", "%s")',
            $user,
            $level
        );

        if ($this->query($input, $output, $sql)) {
            $io->success('User access rights saved');
        } else {
            $io->error('Something went wrong');
        }
    }
}
