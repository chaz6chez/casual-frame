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

/**
 * Class ApplicationFactory
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 * @package Kernel
 */
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

    /**
     * 主程序启动器
     */
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
                }
                if(
                    $handle instanceof ListenerInterface and
                    $handle instanceof AbstractProcess and
                    isset($config['listen'])
                ){
                    $handle->setSocketName($config['listen']);
                }
            }
        }catch (\Throwable $throwable){
            exit($throwable->getMessage());
        }
        AbstractProcess::runAll();
    }

    /**
     * 入口启动器
     * @return Application
     */
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

    /**
     * config init
     */
    protected function _config(){
        Config::load(config_path());
    }

    /**
     * env init
     */
    protected function _env(){
        Env::load(bin_path() . '/.env');
    }
}