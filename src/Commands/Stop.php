<?php
declare(strict_types=1);

namespace Kernel\Commands;

use Kernel\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Stop extends Command
{
    protected static $defaultName = 'stop';

    protected function configure() : void{
        $this
            ->addOption('graceful', 'g',InputOption::VALUE_NONE, 'graceful stop')
            ->setDescription('Stop the application')
            ->setHelp("This command allows you to stop the application");
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        ApplicationFactory::application(null, true);
        return Command::SUCCESS;
    }
}