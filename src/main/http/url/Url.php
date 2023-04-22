<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22
 * Time: 16:54
 */

namespace lanlj\fw\http\url;

use lanlj\fw\core\Arrays;
use lanlj\fw\core\Strings;

final class Url
{
    const SCHEME = 0;
    const HOST = 1;
    const PORT = 2;
    const PATH = 3;
    const QUERY = 4;

    /**
     * @var Arrays
     */
    private $options;

    /**
     * @var Arrays
     */
    private $attributes;

    /**
     * Url constructor.
     * @param null $url
     */
    public function __construct($url = null)
    {
        $arr = array();
        if (!is_null($url))
            $arr = parse_url((new Strings($url))->trim()->replace('\\', '/'));
        $this->attributes = new Arrays($arr);
        $this->options = new Arrays(['scheme', 'host', 'port', 'path', 'query']);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * @return string
     */
    public function build()
    {
        $scheme = $this->get(self::SCHEME, 'http');
        $host = $this->get(self::HOST, 'localhost');
        $port = $this->get(self::PORT, 80);
        $path = $this->get(self::PATH, '/');
        $query = $this->get(self::QUERY);
        return (new Strings($scheme))->concat('://')
            ->concat($host)->concat(in_array($port, [80, 443]) ? '' : ":$port")
            ->concat($path)->concat(in_array($query, [null, '']) ? '' : "?$query")->getString();
    }

    /**
     * @param int $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->attributes->get($this->options->get($name), $default);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        return $this->getParamList()->get($name, $default);
    }

    /**
     * @return Arrays
     */
    public function getParamList()
    {
        $query = $this->get(self::QUERY);
        parse_str($query, $params);
        return new Arrays($params);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Url
     */
    public function addParam($name, $value)
    {
        return $this->set(self::QUERY, $this->getParamList()->add($value, $name)->toQueryString('&', 'p'));
    }

    /**
     * @param int $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->attributes->add($value, $this->options->get($name));
        return $this;
    }

    /**
     * @param array $paramList
     * @return Url
     */
    public function addParamList(array $paramList)
    {
        return $this->set(self::QUERY, $this->getParamList()->addAll($paramList)->toQueryString('&', 'p'));
    }

    /**
     * @param string $name
     * @return Url
     */
    public function removeParam($name)
    {
        return $this->set(self::QUERY, $this->getParamList()->remove($name)->toQueryString('&', 'p'));
    }

    /**
     * @param array $nameList
     * @return Url
     */
    public function removeParamList(array $nameList)
    {
        return $this->set(self::QUERY, $this->getParamList()->removeAll($nameList)->toQueryString('&', 'p'));
    }

    /**
     * @param string $anotherPath
     * @return Url
     */
    public function replacePath($anotherPath)
    {
        $pathInfo = new self($anotherPath);
        if (!is_null($pathInfo->get(self::SCHEME))) {
            $this->attributes = $pathInfo->attributes;
            return $this;
        }
        $anotherPath = (new Strings($anotherPath))->trim()->replace('\\', '/');
        if ($anotherPath->startsWith('/')) {
            $path = $anotherPath->getString();
        } else {
            $path = dirname($this->get(self::PATH)) . '/' . $anotherPath;
        }
        $path = (new Strings($path))->replace('\\', '/');
        $rst = array();
        $pathArray = $path->split('/');
        if (!$pathArray[0]) $rst[] = '';
        foreach ($pathArray as $key => $dir) {
            if ($dir == '..') {
                if (end($rst) == '..') {
                    $rst[] = '..';
                } elseif (!array_pop($rst)) {
                    $rst[] = '..';
                }
            } elseif (($dir || $dir == '0') && $dir != '.') {
                $rst[] = $dir;
            }
        }
        if (!end($pathArray)) $rst[] = '';
        $rst = array_diff($rst, ['..']);
        if (reset($rst)) array_unshift($rst, '');
        return $this->set(self::PATH, (new Arrays($rst))->concatByCustom('/'));
    }
}