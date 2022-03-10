<?php
declare(strict_types=1);

namespace Kernel\Commands;

use Kernel\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Reload extends Command
{
    protected static $defaultName = 'reload';

    protected function configure() : void
    {
        $this
            ->addOption('graceful', 'g', InputOption::VALUE_NONE, 'graceful reload')
            ->setDescription('Reload the application. Use mode -g to reload gracefully.')
            ->setHelp("This command allows you to reload the application");
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        ApplicationFactory::application(null, true);
        return Command::SUCCESS;
    }
}