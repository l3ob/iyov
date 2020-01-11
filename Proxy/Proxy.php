<?php
namespace Proxy;

use Library\Channel;
use Library\Config;
use Library\GatewayClient;
use Library\Http\Request;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Protocols\Http;


/**
 * 代理服务基类
 */
class Proxy {
	/**
	 * 链接实例
	 * 
	 * @var array array(connection => Proxy)
	 */
	protected static $instances = array();

	/**
	 * 客户端链接
	 *
	 * @var AsyncTcpConnection
	 */
	public $connection = null;

	/**
	 * 全局数据统计，并发送给统计进程
	 *
	 * @var array
	 */
	public static $statisticData = array();

	/**
	 * 应用层协议
	 *
	 * @var string
	 */
	public $protocol = '';


    /**
     * 数据包缓存
     * @var string
     */
    protected $buffer = '';

    public function channel($data)
    {
        $this->buffer .= $data;
        $request = new Request($this->buffer);
        if ($request->getMethod() !== 'CONNECT') {
            $length = Http::input($this->buffer, $this->connection);
        } else {
            $length = strlen($this->buffer) ;
            $this->connection->send("HTTP/1.1 200 Connection Established\r\n\r\n", true);
        }
        $this->buffer = substr($this->buffer, $length);
        Channel::processor($request->data());
        $destination = Channel::channel($request);
        Channel::pipe($this->connection, $destination);
        $destination->connect();
        $destination->send($request->data());
    }

    /**
     * 将数据发送给统计进程
     */
    public static function Broadcast()
    {
        ksort(static::$statisticData); // 按时间排序
        GatewayClient::send(json_encode(static::$statisticData)); //JSON_UNESCAPED_SLASHES|
        static::$statisticData = [];
    }

	/**
	 * 初始化代理实例
	 *
	 * @param object $connection
	 * @return self
	 */
	public static function instance($connection)
	{
		if (!isset(static::$instances[$connection->id])) {
			static::$instances[$connection->id] = new static;
			static::$instances[$connection->id]->connection = $connection;
		}
		
		return static::$instances[$connection->id];
	}

	/**
	 * 销毁代理实例
	 *
	 * @param object $connection
	 */
	public static function unInstance($connection)
	{
		unset(static::$instances[$connection->id]);
	}

	/**
	 * 过滤掉不统计的域名信息
	 *
	 * @param string $host
	 * @return bool
	 */
	public function filter($host)
	{
		return in_array($host, Config::get('Iyov.Web.domain'));
	}
}