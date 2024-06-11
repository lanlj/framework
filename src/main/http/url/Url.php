<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22
 * Time: 16:54
 */

namespace lanlj\fw\http\url;

use lanlj\fw\core\{Arrays, Strings};

final class Url
{
    const SCHEME = 0;
    const HOST = 1;
    const PORT = 2;
    const PATH = 3;
    const QUERY = 4;
    private const OPTIONS = ['scheme', 'host', 'port', 'path', 'query'];

    /**
     * @var Arrays
     */
    private static ?Arrays $options = null;

    /**
     * @var Arrays
     */
    private Arrays $attributes;

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
        if (is_null(self::$options))
            self::$options = new Arrays(self::OPTIONS);
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
     * @param int $name
     * @param mixed $default
     * @return mixed
     */
    public function get(int $name, $default = null)
    {
        return $this->attributes->get(self::$options->get($name), $default);
    }

    /**
     * @param string|int $name
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
    public function addParam(?string $name, $value): self
    {
        return $this->set(self::QUERY, $this->getParamList()->add($value, $name)->toQueryString('&', 'p'));
    }

    /**
     * @param int $name
     * @param mixed $value
     * @return $this
     */
    public function set(int $name, $value): self
    {
        $this->attributes->add($value, self::$options->get($name));
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
    public function removeParam(?string $name): self
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
    public function replacePath(?string $anotherPath): self
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