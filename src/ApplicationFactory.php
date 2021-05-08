<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Commands\Connections;
use Kernel\Commands\Reload;
use Kernel\Commands\Restart;
use Kernel\Commands\Start;
use Kernel\Commands\Status;
use Kernel\Commands\Stop;
use Kernel\Protocols\HandlerInterface;
use Kernel\Protocols\ListenerInterface;
use Symfony\Component\Console\Application;

class ApplicationFactory
{
    public static $name    = '3y-clearing-server';
    public static $version = '1.0.0';

    protected static $_commands = [
        Start::class,
        Stop::class,
        Status::class,
        Restart::class,
        Reload::class,
        Connections::class
    ];
    protected $_app;

    /**
     * @return string[]
     */
    public static function commands() : array
    {
        return self::$_commands;
    }

    public static function application(){
        $process = Config::get('process');
        try {
            foreach ($process as $name => $config){
                $handle = Container::instance()->make($config['handler']);
                if($handle instanceof AbstractProcess){
                    $handle->name = $name ?? 'unknown';
                    $handle->count = isset($config['count']) ? $config['count'] : 1;
                    $handle->reloadable = isset($config['reloadable']) ? $config['reloadable'] : true;
                    $handle->reusePort = isset($config['reusePort']) ? $config['reusePort'] : true;
                    if(
                        $handle instanceof ListenerInterface and
                        isset($config['listen'])
                    ){
                        $handle->setSocketName($config['listen']);
                    }
                }
            }
        }catch (\Throwable $throwable){
            exit($throwable->getMessage());
        }
        AbstractProcess::runAll();
    }

    public function __invoke() : Application
    {
        $this->_env();
        $this->_config();
        $this->_app = new Application(static::$name, static::$version);
        foreach (self::commands() as $command){
            $this->_app->add(new $command);
        }
        return $this->_app;
    }

    protected function _config(){
        Config::load(config_path());
    }

    protected function _env(){
        Env::load(bin_path() . '/.env');
    }
}