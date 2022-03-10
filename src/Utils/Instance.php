<?php
# -------------------------- #
#  Name: chaz6chez           #
#  Email: admin@chaz6chez.cn #
#  Date: 2018/10/20            #
# -------------------------- #
namespace Kernel\Utils;

use Kernel\Container;

/**
 * 单例容器
 *
 *  1.使用享元模式开发，所有子类公用一个容器，共同管理
 *  2.内部实现计数 GC，自动控制其单例容器释放内存
 *  3.instanceClean()、instanceRemove()方法可主动释放内存
 *  4.getInstances()方法可获取当前容器情况
 *
 *  注.
 *      1.以上所说的内存释放在PHP GC前提下实现
 *
 * Class Instance
 * @package core\lib
 */
abstract class Instance{

    protected $_config         = [];

    protected static $_time    = 0;     # 当前时间
    protected static $_class   = null;  # 最后一次唤起的类名
    protected static $_parent  = null;  # 最后一次唤起的父类


    /**
     * Service constructor.
     */
    public function __construct() {
        self::$_class = get_called_class();
        self::$_parent = get_parent_class(self::$_class);
        self::now();

        $this->_initConfig();
    }

    /**
     * 载入配置内容
     */
    abstract protected function _initConfig();

    /**
     * 读取配置
     * @param string|null $key
     * @param null $default
     * @return array|mixed|null|object
     */
    public function getConfig(?string $key = null, $default = null) {
        if(!$key){
            return $this->_config;
        }
        return array_key_exists($key, $this->_config) ? $this->_config[$key] : $default;
    }

    /**
     * 动态改变设置
     * @param $key
     * @param $value
     */
    public function setConfig($key, $value) {
        $this->_config[$key] = $value;
    }

    /**
     * @param array $configs
     */
    public function setConfigs(array $configs){
        $this->_config = $configs;
    }

    /**
     * 获取时间
     * @return int
     */
    public static function now() : int
    {
        return self::$_time = isset($GLOBALS['NOW_TIME']) ? (int)$GLOBALS['NOW_TIME'] : time();
    }

    /**
     * 单例模式
     * @return static
     */
    final public static function instance() : Instance
    {
        self::$_class = get_called_class();
        /** @var static $res */
        $res = Co()->get(self::$_class);
        return $res;
    }

    /**
     * new
     * @return static
     */
    final public static function factory() : Instance
    {
        self::$_class = get_called_class();
        /** @var static $res */
        $res = make(self::$_class);
        return $res;
    }
}