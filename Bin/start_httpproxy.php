<?php

use Library\Config;
use Library\Protocol\Http;
use Proxy\HttpProxy;
use \Workerman\Worker;
use \Workerman\Lib\Timer;

// 自动加载类
require_once __DIR__ . '/../vendor/autoload.php';

$http_proxy_worker = new Worker('tcp://' . Config::get('Iyov.Proxy.host') . ':' . Config::get('Iyov.Proxy.port'));

$http_proxy_worker->count = 5;

$http_proxy_worker->name = 'iyov-http-proxy';

$http_proxy_worker->onWorkerStart = function() {
	Timer::add(1, array(HttpProxy::class, 'Broadcast'), array(), true);
};

$http_proxy_worker->onConnect = function($connection) {
	HttpProxy::instance($connection)->initClientCapture();
};

$http_proxy_worker->onMessage = function($connection, $buffer) {
    $proxy = HttpProxy::instance($connection);
	if (!$proxy->asyncTcpConnection) {
        $proxy->data .= $buffer;
		if (!($length = Http::input($proxy->data))) {
			return ;
		}

        $proxy->requestProcess(HttpProxy::instance($connection)->data);
        $proxy->data = '';
	}
};

$http_proxy_worker->onClose = function($connection) {
	HttpProxy::instance($connection)->unInstance($connection);
};
