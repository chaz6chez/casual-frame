<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Commands\Connections;
use Kernel\Commands\Reload;
use Kernel\Commands\Restart;
use Kernel\Commands\Routes;
use Kernel\Commands\Start;
use Kernel\Commands\Status;
use Kernel\Commands\Stop;
use Symfony\Component\Console\Command\Command;

final class Commands
{
    /**
     * @var string[]
     */
    protected static $_commands = [
        Start::class,
        Stop::class,
        Status::class,
        Restart::class,
        Reload::class,
        Connections::class
    ];

    /**
     * @var Command[]
     */
    protected static $_commandObj = [];

    /**
     * @return Command[]
     */
    public static function commands() : array
    {
        foreach (self::$_commands as $command){
            self::$_commandObj[] = make($command);
        }
        return self::$_commandObj;
    }

    /**
     * @param Command $command
     */
    public static function register(Command $command)
    {
        self::$_commandObj[] = $command;
    }
}