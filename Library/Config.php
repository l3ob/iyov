<?php
namespace Library;

// 配置文件读取
class Config {

    /**
     * 配置文件目录
     *
     * @var string
     */
    private static $configPath = 'Config';

    /**
     * Project Config
     *
     * @var array
     */
    private static $config = array();

    /**
     * ENV
     * @var array
     */
    private static $iniConf = [];

    /**
     * 设置配置文件读取目录
     * @param string $config
     * @throws \Exception
     */
    public static function setConfigPath(string $config = '')
    {
        if (!is_dir($config)) {
            throw new \Exception("目录不存在");
        }

        self::$configPath = $config;
    }

    /**
     * 获取INI配置项
     * @param string $name
     * @param string $default
     * @return mixed|string
     */
    public static function getEnv(string $name, $default = '')
    {
        if (empty(self::$iniConf)) {
            self::$iniConf = self::initial('.env.ini');
        }

        return self::$iniConf[$name] ?? $default;
    }

    /**
     * 获取配置
     *
     * @param string $configClass
     * @throws \Exception
     * @return array
     */
    protected static function config($configClass = '')
    {
        $path = self::$configPath . '/' . $configClass . '.php';
        if (!self::$configPath || !file_exists($path)) {
            throw new \Exception("Config namespace is empty or {$path} not exist.");
        }

        $key = serialize($path);
        if (!isset(self::$config[$key])) {
            self::$config[$key] = require_once $path;
        }

        return self::$config[$key];
    }

    /**
     * 获取配置项
     *
     * @param string $configName
     * @param mixed  $default
     * @throws \Exception
     * @return mixed
     */
    public static function get($configName = '', $default = '')
    {
        if (!$configName) {
            throw new \Exception('配置项为空');
        }

        $spices = explode('.', $configName);
        $config = self::config(array_shift($spices));
        while(count($spices)) {
            $key = array_shift($spices);
            if (!isset($config[$key]) && count($spices)) {
                throw new \Exception("{$configName} not exist.");
            }
            if (count($spices)) {
                $config = $config[$key];
                continue ;
            }
            return $config[$key] ?? $default;
        }
    }

    /**
     * 初始化配置文件 从INI文件读取配置
     * @param string $path
     * @return array
     * @throws \Exception
     */
    protected static function initial(string $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new \Exception("ENV配置文件不存在.");
        }

        return parse_ini_file($path);
    }
}