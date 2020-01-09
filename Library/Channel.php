<?php
namespace Library;
use Library\Http\Request;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;

/**
 * Created by PhpStorm
 * @author cp
 * @date 2020/1/2
 */
class Channel {
    /**
     * @var array [TcpConnection, ..., TcpConnection]
     */
    private static $destinationMap = [];

    /**
     * 初始化管道
     * @param Request $request
     * @return AsyncTcpConnection
     */
    public static function channel(Request $request)
    {
        if (isset(self::$destinationMap[$request->getHost()])) {
            return self::$destinationMap[$request->getHost()];
        }

        self::$destinationMap[$request->getHost()] = new AsyncTcpConnection("tcp://".$request->getHost());
        return self::$destinationMap[$request->getHost()];
    }

    /**
     * 构建通信管道
     * @param AsyncTcpConnection $connectionClient
     * @param AsyncTcpConnection $connectionServer
     */
    public static function pipe(TcpConnection $connectionClient, TcpConnection$connectionServer)
    {
        $connectionClient->pipe($connectionServer);
        $connectionServer->pipe($connectionClient);
    }

}
