<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Commands\Connections;
use Kernel\Commands\Reload;
use Kernel\Commands\Restart;
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
        if(!self::$_commandObj){
            foreach (self::$_commands as $command){
                self::$_commandObj[] = make($command);
            }
        }
        return self::$_commandObj;
    }

    /**
     * @param string $class
     */
    public static function register(string $class)
    {
        if(class_exists($class, false)){
            if(($command = make($class)) instanceof Command){
                self::$_commandObj[] = $command;
            }
        }
    }
}