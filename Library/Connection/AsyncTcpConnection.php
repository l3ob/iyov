<?php
namespace Library\Connection;
use Workerman\Connection\TcpConnection;

/**
 * Created by PhpStorm
 * @author cp
 * @date 2019/12/4
 */
class AsyncTcpConnection extends \Workerman\Connection\AsyncTcpConnection {
    /**
     * 重载管道
     * @param TcpConnection|static $dest
     */
    public function pipe($dest)
    {
        $source              = $this;
        $this->onMessage     = function ($source, $data) use ($dest) {
            $dest->send($data);
            call_user_func($source->onMessageCapture, $data);
        };
        $this->onClose       = function ($source) use ($dest) {
            $dest->destroy();
        };
        $dest->onBufferFull  = function ($dest) use ($source) {
            $source->pauseRecv();
        };
        $dest->onBufferDrain = function ($dest) use ($source) {
            $source->resumeRecv();
        };
    }
}
