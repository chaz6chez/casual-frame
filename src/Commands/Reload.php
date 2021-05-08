<?php
declare(strict_types=1);

namespace Internal\Kernel\Commands;

use Internal\Kernel\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reload extends Command
{
    protected static $defaultName = 'reload';

    protected function configure() : void{
        $this
            ->setDescription('Reload the application')
            ->setHelp("This command allows you to reload the application");
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        ApplicationFactory::application();
        return Command::SUCCESS;
    }
}