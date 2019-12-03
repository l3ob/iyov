<?php
use \Applications\Lib\Config;

return [
    'Web'=>[
        'domain' => Config::getEnv('WEB_DOMAIN', ['test.iyov.io']),
        'host' => Config::getEnv('WEB_HOST', '0.0.0.0'),
        'port' => Config::getEnv('WEB_LISTEN', 8080),
    ],
    'Proxy'=> [
        'host' => Config::getEnv('PROXY_HOST', '0.0.0.0'),
        'port' => Config::getEnv('PROXY_LISTEN', 9733),
    ],
    'Gateway' => [
        'host' => Config::getEnv('GATEWAY_HOST', '0.0.0.0'),
        'port' => Config::getEnv('GATEWAY_LISTEN', 4355)
    ],
];