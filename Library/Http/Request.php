<?php
namespace Library\Http;

class Request {
    protected $_buffer = '';
    protected $headers = [];
    protected $content = '';

    protected $method = '';
    protected $url = '';
    protected $protocol = '';

    public function __construct(string $data)
    {
        $this->_buffer = $data;
        list($headers, $this->content) = explode("\r\n\r\n", $data, 2);
        $headers = explode("\r\n", $headers);
        list($this->method, $this->url, $this->protocol) = explode(" ", array_shift($headers));
        foreach ($headers as $line) {
            list($k, $v) = explode(": ", $line);
            $this->headers[$k] = $v;
        }
    }

    /**
     * 获取Header信息,不传key时返回全部Header
     *
     * @param string $key
     * @return array|mixed
     */
    public function header($key = '')
    {
        return $this->headers[$key] ?? $this->headers;
    }

    /**
     * @return string
     */
    public function data()
    {
        return $this->_buffer;
    }

    public function getHost()
    {
        return $this->headers['Host'];
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }
}