<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/29
 * Time: 1:48
 */

namespace lanlj\fw\http;

use lanlj\fw\core\Arrays;
use lanlj\fw\core\Strings;
use lanlj\fw\http\storage\Cookie;
use lanlj\fw\http\storage\Session;
use lanlj\fw\http\url\Url;

final class Request
{
    const GET = 'GET';
    const POST = 'POST';
    const HEAD = 'HEAD';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const OPTIONS = 'OPTIONS';

    /**
     * @var array
     */
    private static $params = [];

    /**
     * @var Arrays
     */
    private $attributes;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->attributes = new Arrays();
    }

    /**
     * @param array $params
     */
    public static function addParams(array $params)
    {
        self::$params = (new Arrays(self::$params))->addAll($params)->getArray();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        return $this->attributes->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes->add($value, $name);
    }

    /**
     * 获取参数值
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        $params = $this->getParamList();
        $r = $params->get($name, $default);
        if (is_array($r)) {
            $arr = new Arrays($r);
            $r = $arr->get($arr->size() - 1, $default);
        }
        return is_string($r) ? trim($r) : $r;
    }

    /**
     * 获取参数列表
     * @return Arrays
     */
    public function getParamList()
    {
        $arrays = new Arrays($_GET);
        $params_list = (new Url($this->getRequestURI()))->get(Url::QUERY);
        if (!is_null($params_list)) {
            parse_str($params_list, $array);
            $arrays->addAll($array);
        }
        $arrays->addAll($_POST)->addAll(self::$params);
//        foreach ($arr as $key => $value) {
//            if (!is_array($value)) {
//                unset($arr[$key]);
//                $arr[$key][] = $value;
//            }
//        }
        return $arrays;
    }

    /**
     * @return string
     */
    public function getRequestURI()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取参数值数组
     * @param string $name
     * @param mixed $default
     * @return array
     */
    public function getParams($name, $default = null)
    {
        $params = $this->getParamList();
        $r = $params->get($name, $default);
        return is_array($r) ? $r : [is_string($r) ? trim($r) : $r];
    }

    /**
     * 获取指定Cookie
     * @param string $name
     * @return mixed
     */
    public function getCookie($name)
    {
        return $this->getCookies()->get($name);
    }

    /**
     * 获取所有Cookie
     * @return Arrays
     */
    public function getCookies()
    {
        return new Arrays($_COOKIE);
    }

    /**
     * 移除所有Cookie
     */
    public function removeCookies()
    {
        foreach ($_COOKIE as $key => $value)
            $this->removeCookie($key);
    }

    /**
     * 移除指定Cookie
     * @param string $name
     * @param Cookie $cookie
     */
    public function removeCookie($name, Cookie $cookie = null)
    {
        $uc = false;
        if (!is_null($cookie)) {
            $uc = true;
            $name = $cookie->getName();
        }
        if ($name != 'PHPSESSID') {
            unset($_COOKIE[$name]);
            if (!$uc) setcookie($name, null, time() - 3600);
            else setcookie($name, null, time() - 3600, $cookie->getPath(), $cookie->getDomain());
        }
    }

    /**
     * 获取请求头部
     * @param string $s
     * @return mixed
     */
    public function getHeader($s)
    {
        return $this->parseHeader()->get($s);
    }

    /**
     * 解析请求头部信息
     * @return Arrays
     */
    private function parseHeader()
    {
        if (!function_exists('getallheaders')) {
            $headers = array();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        } else $headers = getallheaders();
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
        } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $headers['Authorization'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }
        return new Arrays($headers);
    }

    /**
     * 获取所有请求头名
     * @return Arrays
     */
    public function getHeaderNames()
    {
        return $this->parseHeader()->getKeys();
    }

    /**
     * 获取字符串请求参数
     * @return string
     */
    public function getQueryString()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * @return string
     */
    public function getRequestURL()
    {
        return (new Strings($this->getScheme()))
            ->concat('://')
            ->concat($this->getServerName())
            ->concat(in_array($this->getServerPort(), [80, 443]) ? '' : ':' . $this->getServerPort())
//            ->concat($_SERVER['PHP_SELF'])
            ->concat($this->getRequestURI())
            ->getString();
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * @return string
     */
    public function getServerPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * @return Arrays
     */
    public function getAttributeNames()
    {
        return $this->attributes->getKeys();
    }

    /**
     * @return Arrays
     */
    public function getParamNames()
    {
        return $this->getParamList()->getKeys();
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return Session::getInstance();
    }

    /**
     * 指定请求方式
     * @param array ...$methods
     */
    public function requestMethods(...$methods)
    {
        if (!in_array($method = $this->getMethod(), $methods))
            die("Request method '$method' not supported.");
    }

    /**
     * 获取请求方式
     * @return string
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}