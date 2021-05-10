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
    public static $name    = 'casual-core';
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
     * 进程启动器
     * @param string|null $app
     */
    public static function application(?string $app = null){
        $process = Config::get('process');
        if($app !== null and !isset($process[$app])){
            exit('Not found the app');
        }
        try {
            foreach ($process as $name => $config){
                if($app !== null and $app !== $name){
                    continue;
                }
                $handle = Container::instance()->make($config['handler']);
                if($handle instanceof AbstractProcess){
                    $handle = ($handle)();
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
     * @param string|null $name
     * @param string|null $version
     * @return Application
     */
    public function __invoke(?string $name = null, ?string $version = null) : Application
    {
        self::$name = $name ?? self::$name;
        self::$version = $version ?? self::$version;
        $this->_env();
        $this->_config();
        $this->_app = new Application(self::$name, self::$version);
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