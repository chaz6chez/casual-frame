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
     * @param string ...$commands
     */
    public static function register(string ...$commands)
    {
        foreach ($commands as $command){
            if(is_string($command) and class_exists($command)){
                if(($command = make($command)) instanceof Command){
                    self::$_commandObj[] = $command;
                }
            }
        }
    }
}