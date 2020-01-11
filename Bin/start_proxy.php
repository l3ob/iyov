<?php

use Library\Config;
use Workerman\Worker;
use Workerman\Lib\Timer;

// 自动加载类
require_once __DIR__ . '/../vendor/autoload.php';

$http_proxy_worker = new Worker('tcp://' . Config::get('Iyov.Proxy.host') . ':' . Config::get('Iyov.Proxy.port'));

$http_proxy_worker->count = 5;

$http_proxy_worker->name = 'Proxy';

$http_proxy_worker->onWorkerStart = function() {
    \Library\Channel::register(array(\Library\Stat::class, 'processor'));
	Timer::add(1, array(\Proxy\Proxy::class, 'Broadcast'), [], true);
};

$http_proxy_worker->onConnect = function($connection) {
    \Proxy\Proxy::instance($connection);
};

$http_proxy_worker->onMessage = function($connection, $data) {
    \Proxy\Proxy::instance($connection)->channel($data);
};

$http_proxy_worker->onClose = function($connection) {
	\Proxy\Proxy::instance($connection)->unInstance($connection);
};
