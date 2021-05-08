<?php
declare(strict_types=1);

namespace Kernel;

/**
 * Class Env
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 * @package Kernel
 */
class Env {
    /**
     * @var bool $_init
     */
    protected static $_init = false;

    /**
     * @var array 环境变量数据
     */
    protected static $_env = [];

    /**
     * @var string
     */
    protected static $_file = '';

    /**
     * @param string $file 设置加载文件
     */
    public static function setFile(string $file) : void {
        self::$_file = $file;
    }

    /**
     * 获取当前加载的文件
     * @return string
     */
    public static function getFile() : string {
        return self::$_file;
    }

    /**
     * init
     */
    private static function _init() : void {
        $_ENV = getenv();
        $env = [];
        foreach ($_ENV as $key => $value){
            $env["ENV_{$key}"] = $value;
        }
        self::$_init = true;
        self::$_env = array_merge(self::$_env,$env);
    }

    /**
     * 读取环境变量定义文件
     * @access public
     * @param  string $file  环境变量定义文件
     * @return void
     */
    public static function load(string $file = ''){
        self::_init();
        self::setFile($file);
        if(file_exists(self::getFile())){
            $env = parse_ini_file(self::getFile(), true);
            self::set($env);
        }
    }

    /**
     * 获取环境变量值
     * @param null $name
     * @param null $default
     * @return array|bool|false|mixed|null|string
     */
    public static function get($name = null, $default = null){
        if (is_null($name)) {
            return self::$_env;
        }
        $name = strtoupper(str_replace('.', '_', $name));
        if (isset(self::$_env[$name])) {
            return self::$_env[$name];
        }
        return $default;
    }

    /**
     * 设置环境变量值
     * @access public
     * @param  string|array  $env   环境变量
     * @param  mixed         $value  值
     * @return void
     */
    public static function set($env, $value = null){
        if($env){
            if (is_array($env)) {
                $env = array_change_key_case($env, CASE_UPPER);

                foreach ($env as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k => $v) {
                            self::$_env[$key . '_' . strtoupper($k)] = $v;
                        }
                    } else {
                        self::$_env[$key] = $val;
                    }
                }
            } else {
                $name = strtoupper(str_replace('.', '_', $env));
                self::$_env[$name] = $value;
            }
        }
    }
}