<?php
declare(strict_types=1);

namespace Internal\Kernel\Commands;

use Internal\Kernel\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Restart extends Command
{
    protected static $defaultName = 'restart';

    protected function configure() : void{
        $this
            ->addOption('daemon', 'd',InputOption::VALUE_NONE, 'graceful stop')
            ->setDescription('Restart the application')
            ->setHelp("This command allows you to restart the application");
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        ApplicationFactory::application();
        return Command::SUCCESS;
    }
}