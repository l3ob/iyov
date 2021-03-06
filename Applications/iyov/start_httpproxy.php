<?php

use \Workerman\Worker;
use \Workerman\Lib\Timer;
use \Applications\Lib\Config;
use \Workerman\Autoloader;
use \Applications\iyov\Lib\Http;
use \Applications\iyov\HttpProxy;

//Config::setNameSpace('Applications\Config');

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

$http_proxy_worker = new Worker('tcp://' . Config::get('Iyov.Proxy.host') . ':' . Config::get('Iyov.Proxy.port'));

$http_proxy_worker->count = 5;

$http_proxy_worker->name = 'iyov-http-proxy';

$http_proxy_worker->onWorkerStart = function() {
	Timer::add(1, array('\Applications\iyov\HttpProxy', 'Broadcast'), array(), true);
};

$http_proxy_worker->onConnect = function($connection) {
	HttpProxy::instance($connection)->initClientCapture();
};

$http_proxy_worker->onMessage = function($connection, $buffer) {
	if (!HttpProxy::instance($connection)->asyncTcpConnection) {
		HttpProxy::instance($connection)->data .= $buffer;
		if (!($length = Http::input(HttpProxy::instance($connection)->data))) {
			return ;
		}

		HttpProxy::instance($connection)->requestProcess(HttpProxy::instance($connection)->data);
		HttpProxy::instance($connection)->data = '';
	}
};

$http_proxy_worker->onClose = function($connection) {
	HttpProxy::instance($connection)->unInstance($connection);
};
