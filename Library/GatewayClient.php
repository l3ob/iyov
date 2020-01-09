<?php
namespace Library;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\AsyncUdpConnection;
use Workerman\Connection\ConnectionInterface;

/**
 * Created by PhpStorm
 * @author cp
 * @date 2019/12/17
 */

class GatewayClient {

    /**
     * 目的地址
     * @var string
     */
    protected $remoteAddress = '';

    /**
     * 目的地址链接
     * @var ConnectionInterface
     */
    protected $connection = null;

    /**
     * 当前实例
     * @var self
     */
    private static $instance = null;

    /**
     * 发送数据
     * @param string $data
     */
    public static function send(string $data)
    {
        if (!self::$instance) {
            self::$instance = new self(self::getRemoteAddress());
        }
        self::$instance->getConnect()->send($data);
    }

    /**
     * 获取目的地址
     * @return string
     */
    public static function getRemoteAddress()
    {
        return Config::get('Iyov.Gateway.protocol')
                . '://'
                . Config::get('Iyov.Gateway.domain')
                .':'
                . Config::get('Iyov.Gateway.port');
    }

    /**
     * 获取目的地址链接
     * @return AsyncTcpConnection|AsyncUdpConnection|ConnectionInterface
     */
    protected function getConnect()
    {
        if (!$this->connection) {
            $this->connection = $this->connect();
        }

        return $this->connection;
    }

    /**
     * 初始化目的地址链接
     * @return AsyncTcpConnection|AsyncUdpConnection
     * @throws \Exception
     */
    private function connect()
    {
        $uri = parse_url($this->remoteAddress);
        switch ($uri['scheme']) {
            case 'http':
            case 'https':
            case 'tcp':
                $connect = new AsyncTcpConnection($this->remoteAddress);
                $connect->connect();
                return $connect;
            case 'udp':
                $connect = new AsyncUdpConnection($this->remoteAddress);
                $connect->connect();
                return $connect;
            default :
                throw new \Exception("invalid gateway scheme");
        }
    }

    public function __construct(string $remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
        $this->connection = $this->connect();
    }

}