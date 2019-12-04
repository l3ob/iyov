<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Library\Config;
use Workerman\WebServer;
use Workerman\Worker;

// WebServer
$web = new WebServer("http://" . Config::get('Iyov.Web.host') . ":" . Config::get('Iyov.Web.port'));

// 4 processes
$web->count = 4;

// Set the root of domains
foreach(Config::get('Iyov.Web.domain') as $domain) {
    $web->addRoot($domain, 'Web');
};

// run all workers
Worker::runAll();