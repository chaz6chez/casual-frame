<?php
declare(strict_types=1);

namespace Kernel\Commands;

use Kernel\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Command
{
    protected static $defaultName = 'start';

    protected function configure() : void{
        $this
            ->addOption('daemon', 'd',InputOption::VALUE_NONE, 'DAEMON mode')
            ->setDescription('Start the application')
            ->setHelp("This command allows you to start the application");
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        ApplicationFactory::application();
        return Command::SUCCESS;
    }
}