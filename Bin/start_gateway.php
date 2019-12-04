<?php

use Library\Config;
use Proxy\Gateway;
use \Workerman\Worker;


// 自动加载类
require_once __DIR__ . '/../vendor/autoload.php';

$gateway_worker = new Worker('websocket://' . Config::get('Iyov.Gateway.host') . ':' . Config::get('Iyov.Gateway.port'));

$gateway_worker->name = 'iyov-gateway';

$gateway_worker->count = 1;

$gateway_worker->onWorkerStart = function($gateway_worker) {
	Gateway::Init($gateway_worker);
};