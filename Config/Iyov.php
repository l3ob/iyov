<?php

use Library\Config;

return [
    'Gateway'=>[ // 内部端口,无需添加env配置,TIPS:稍加更改可支持分布式部署
        'protocol' => 'tcp', // 上报,指定传送协议
        'port'=> Config::getEnv('GATEWAY_LISTEN', 9388),
        'domain' => Config::getEnv('GATEWAY_ADDRESS', '127.0.0.1'),
    ],
    'Web'=>[ // 网页地址
        'domain' => Config::getEnv('WEB_DOMAIN', ['test.iyov.io']),
        'host' => Config::getEnv('WEB_HOST', '0.0.0.0'),
        'port' => Config::getEnv('WEB_LISTEN', 8080),
    ],
    'Proxy'=> [ // 代理地址
        'host' => Config::getEnv('PROXY_HOST', '0.0.0.0'),
        'port' => Config::getEnv('PROXY_LISTEN', 9733),
    ],
    'WebSocket' => [ // WebSocket
        'host' => Config::getEnv('WEBSOCKET_HOST', '0.0.0.0'),
        'port' => Config::getEnv('WEBSOCKET_LISTEN', 4355)
    ],
];