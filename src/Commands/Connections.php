<?php
declare(strict_types=1);

namespace Internal\Kernel\Commands;

use Internal\Kernel\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Connections extends Command
{
    protected static $defaultName = 'connections';

    protected function configure() : void{
        $this
            ->setDescription('Display connections status')
            ->setHelp("This command allows you to check the connections status");
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        ApplicationFactory::application();
        return Command::SUCCESS;
    }
}