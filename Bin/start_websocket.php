<?php

use Library\Config;
use Proxy\Gateway;
use \Workerman\Worker;


// 自动加载类
require_once __DIR__ . '/../vendor/autoload.php';

$gateway_worker = new Worker('websocket://' . Config::get('Iyov.WebSocket.host') . ':' . Config::get('Iyov.WebSocket.port'));

$gateway_worker->name = 'WebSocket';

$gateway_worker->count = 1; // 暂不支持多进程

$gateway_worker->onWorkerStart = function($gateway_worker) {
	Gateway::listen($gateway_worker);
};