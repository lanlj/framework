<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/30
 * Time: 1:37
 */

namespace lanlj\fw\http;

use lanlj\fw\core\Arrays;
use lanlj\fw\http\storage\Cookie;
use lanlj\fw\json\Json;
use lanlj\fw\util\StringUtil;
use lanlj\fw\util\Utils;

final class Response
{
    /**
     * Response constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getHeader($name)
    {
        return $this->parseHeader()->get($name);
    }

    /**
     * @return Arrays
     */
    private function parseHeader()
    {
        $headers = new Arrays();
        foreach (headers_list() as $header) {
            if (Utils::isEmpty($header))
                continue;
            $item = explode(': ', $header, 2);
            if (count($item) == 2)
                $headers->add(trim($item[1]), $item[0]);
            else $headers->add(trim($header));
        }
        return $headers;
    }

    /**
     * @return Arrays
     */
    public function getHeaderNames()
    {
        return $this->parseHeader()->getKeys();
    }

    /**
     * @param mixed $value
     */
    public function writeJson($value)
    {
        $this->setContentType('text/json, application/json; charset=utf-8');
        echo Json::toJsonString($value, false, true);
    }

    /**
     * @param string $contentType
     * @return Response
     */
    public function setContentType($contentType)
    {
        return $this->setHeader('Content-Type', $contentType);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setHeader($name, $value)
    {
        if (!headers_sent())
            header($name . ': ' . $value);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function removeHeader($name)
    {
        header_remove($name);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeHeaders()
    {
        header_remove();
        return $this;
    }

    /**
     * @param mixed $value
     */
    public function write($value)
    {
        echo StringUtil::toString($value);
    }

    /**
     * @param Cookie $cookie
     * @return $this
     */
    public function addCookie(Cookie $cookie)
    {
        $name = $cookie->getName();
        $value = $cookie->getValue();
        $_COOKIE[$name] = $value;
        setcookie($name, $value, $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->parseHeader()->get('Content-Type');
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value)
            $this->setHeader($name, $value);
        return $this;
    }

    /**
     * @param string $location
     */
    public function sendLocation($location)
    {
        $this->setHeader('Location', $location);
        exit();
    }
}