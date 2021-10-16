<?php
declare(strict_types=1);

namespace Kernel\Commands;

use Kernel\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends Command
{
    protected static $defaultName = 'status';

    protected function configure() : void{
        $this
            ->addOption('daemon', 'd',InputOption::VALUE_NONE, 'DAEMON mode')
            ->setDescription('Display statistics')
            ->setHelp("This command allows you to view statistics");
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        ApplicationFactory::application(null, true);
        return Command::SUCCESS;
    }
}