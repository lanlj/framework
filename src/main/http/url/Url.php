<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22
 * Time: 16:54
 */

namespace lanlj\fw\http\url;

use lanlj\fw\base\{Arrays, Strings};

final class Url
{
    const SCHEME = 'scheme';
    const HOST = 'host';
    const PORT = 'port';
    const USER = 'user';
    const PASS = 'pass';
    const QUERY = 'query';
    const PATH = 'path';
    const FRAGMENT = 'fragment';

    /**
     * @var Arrays
     */
    private Arrays $attributes;

    /**
     * Url constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->attributes = new Arrays(parse_url((new Strings($url))->trim()->replace('\\', '/')));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * @return string
     */
    public function build(): string
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
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->attributes->get($name, $default);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam(string $name, $default = null)
    {
        return $this->getParamList()->get($name, $default);
    }

    /**
     * @return Arrays
     */
    public function getParamList(): Arrays
    {
        $query = $this->get(self::QUERY);
        parse_str($query, $params);
        return new Arrays($params);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function addParam(string $name, $value): self
    {
        return $this->set(self::QUERY, $this->getParamList()->add($value, $name)->toQueryString('&', 'p'));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set(string $name, $value): self
    {
        $this->attributes->add($value, $name);
        return $this;
    }

    /**
     * @param array $paramList
     * @return self
     */
    public function addParamList(array $paramList): self
    {
        return $this->set(self::QUERY, $this->getParamList()->addAll($paramList)->toQueryString('&', 'p'));
    }

    /**
     * @param string $name
     * @return Url
     */
    public function removeParam(string $name): self
    {
        return $this->set(self::QUERY, $this->getParamList()->remove($name)->toQueryString('&', 'p'));
    }

    /**
     * @param array $nameList
     * @return self
     */
    public function removeParamList(array $nameList): self
    {
        return $this->set(self::QUERY, $this->getParamList()->removeAll($nameList)->toQueryString('&', 'p'));
    }

    /**
     * @param string $anotherPath
     * @return self
     */
    public function replacePath(string $anotherPath): self
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