<?php
/**
 * run with command 
 * php start.php start
 */

require_once __DIR__ . '/vendor/autoload.php';
ini_set('display_errors', 'on');
use Workerman\Worker;

// 标记是全局启动
define('GLOBAL_START', 1);

// 加载所有Applications/*/start.php，以便启动所有服务
foreach(glob(__DIR__.'/Bin/start_*.php') as $start_file)
{
    require_once $start_file;
}
// 运行所有服务
Worker::runAll();