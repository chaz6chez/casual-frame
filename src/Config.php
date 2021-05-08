<?php
declare(strict_types=1);

namespace Kernel;

/**
 * Class Config
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 * @package Kernel
 */
class Config {

    /**
     * @var array
     */
    protected static $_config = [];

    /**
     * @param string $path
     * @param array $excludes
     */
    public static function load(string $path, array $excludes = [])
    {
        if (\strpos($path, 'phar://') === false) {
            foreach (\glob($path . '/*.php') as $file) {
                $basename = \basename($file, '.php');
                if (\in_array($basename, $excludes)) {
                    continue;
                }
                $config = include $file;
                static::$_config[$basename] = $config;
            }
        } else {
            $handler = \opendir($path);
            while (($filename = \readdir($handler)) !== false) {
                if ($filename != '.' && $filename != '..') {
                    $basename = \basename($filename, '.php');
                    if (\in_array($basename, $excludes)) {
                        continue;
                    }
                    $config = include($path . DIRECTORY_SEPARATOR . $filename);
                    static::$_config[$basename] = $config;
                }
            }
            \closedir($handler);
        }
    }

    /**
     * @param string|null $key
     * @param null $default
     * @return mixed
     */
    public static function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return static::$_config;
        }
        $keys = \explode('.', $key);
        $value = static::$_config;
        foreach ($keys as $index) {
            if (!isset($value[$index])) {
                return $default;
            }
            $value = $value[$index];
        }
        return $value;
    }

    /**
     * @param string $path
     * @param array $excludes
     */
    public static function reload(string $path, array $excludes = [])
    {
        static::$_config = [];
        static::load($path, $excludes);
    }
}
