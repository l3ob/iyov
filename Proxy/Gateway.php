<?php
namespace Proxy;

use Library\Config;
use Workerman\Worker;
use Workerman\Lib\Timer;

class Gateway {

	/**
	 * 内部通信worker，接收来自代理进程的数据
	 *
	 * @var object
	 */
	private static $internalWorker = null;

    /**
     * 广播时间间隔 单位:秒
     *
     * @var int
     */
    public static $interval = 1;

	/**
	 * 统计数据汇总
	 *
	 * @var array
	 */
	protected static $globalData = [];

	/**
	 * Gatewayworker，与PC端建立websocket连接
	 *
	 * @var Worker
	 */
	protected static $gatewayWorker = null;

    /**
     *
     * @param $worker
     */
	public static function listen($worker)
	{
        static::$gatewayWorker = $worker;

        // 初始化内部通信,buffer统计数据
        static::$internalWorker = new Worker(self::internalAddress());
        static::$internalWorker->onMessage = function($connection,$data) {
            $data = json_decode($data, true);
            if (empty($data)) { return; }
            self::$globalData = self::$globalData + $data;
        };
        static::$internalWorker->listen();
        static::$internalWorker->run();
	}

    /**
     * 广播统计数据给网页
     */
	public static function Broad()
	{
		if (empty(static::$gatewayWorker->connections)) { // 清空
			self::$globalData = [];
			return ;
		}

		// 向所有连接广播数据
		foreach(static::$gatewayWorker->connections as $connection) {
			$connection->send(json_encode(self::$globalData));
		}
		self::$globalData = [];
	}

    /**
     * 获取监听地址
     * @return string
     */
	protected static function internalAddress()
    {
        return Config::get('Iyov.Gateway.protocol') . '://0.0.0.0:' . Config::get('Iyov.Gateway.port');
    }
}