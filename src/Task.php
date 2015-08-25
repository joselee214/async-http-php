<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/8/22
 * Time: 17:59
 */

namespace Jenner\Http;


class Task implements TaskInterface
{
    protected $method;

    protected $url;

    protected $proxy_ip = null;

    protected $proxy_port = null;

    protected $timeout = 10;

    protected $transfer_timeout = 600;

    protected $params = null;

    const METHOD_POST = "post";

    const METHOD_GET = "get";

    public static function createGet($url, $params = null, $timeout = 10, $transfer_timeout = 600)
    {
        return new Task(self::METHOD_GET, $url, $params, $timeout, $transfer_timeout);
    }

    public static function createPost($url, $params = null, $timeout = 10, $transfer_timeout = 600)
    {
        return new Task(self::METHOD_POST, $url, $params, $timeout, $transfer_timeout);
    }

    protected function __construct($method = Task::METHOD_GET, $url, $params = null, $timeout = 10, $transfer_timeout = 600)
    {
        $this->method = $method;
        $this->url = $url;
        $this->params = $params;
        $this->timeout = $timeout;
        $this->transfer_timeout = $transfer_timeout;
    }

    public function setProxy($host, $port)
    {
        $this->proxy_ip = $host;
        $this->proxy_port = $port;
    }

    public function setTimeout($timeout = 10, $transfer_timeout)
    {
        $this->timeout = $timeout;
        $this->transfer_timeout = $transfer_timeout;
    }

    public function setTransferTimeout($transfer_timeout = 600)
    {
        $this->transfer_timeout = $transfer_timeout;
    }

    public function setParams($params = null)
    {
        $this->params = $params;
    }

    /**
     * get curl resource
     * @return resource curl
     */
    public function getTask()
    {
        $ch = curl_init();

        if ($ch === false) {
            throw new \RuntimeException("init curl failed");
        }

        if (!is_null($this->proxy_ip) && !is_null($this->proxy_port)) {
            $proxy = "http://{$this->proxy_ip}:{$this->proxy_port}";
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        $url = $this->url;
        if ($this->method == self::METHOD_GET && !is_null($this->params)) {
            $url .= http_build_query($this->params);
        }
        if ($this->method == self::METHOD_POST && !is_null($this->params)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (is_array($this->params)) {
                $post_field = http_build_query($this->params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->transfer_timeout);

        return $ch;
    }
}