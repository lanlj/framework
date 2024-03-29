<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/29
 * Time: 1:02
 */

namespace lanlj\fw\core;

use lanlj\fw\json\Json;
use lanlj\fw\util\ArrayUtil;
use lanlj\fw\util\Utils;
use ReflectionException;
use ReflectionFunction;

final class Arrays
{
    /**
     * @var array
     */
    private $array;

    /**
     * Arrays constructor.
     * @param array|object $array
     */
    public function __construct($array = array())
    {
        if (!is_array($array)) $array = ArrayUtil::toArray($array);
        $this->array = $array;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return Json::toJsonString($this->array);
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @param string $rootNode
     * @return string
     */
    public function toXML($rootNode = "xml")
    {
        return ArrayUtil::toXML($this->array, null, null, [ArrayUtil::XML_ROOT => $rootNode]);
    }

    /**
     * @param mixed $needle
     * @param bool $strict
     * @param bool $regex
     * @return bool
     */
    public function contains($needle, $strict = false, $regex = false)
    {
        if (!$regex)
            return in_array($needle, $this->array, $strict);
        foreach ($this->array as $value) {
            if (preg_match($needle, $value))
                return true;
        }
        return false;
    }

    /**
     * @param mixed $value
     * @param int|string $key
     * @return $this
     */
    public function add($value, $key = null)
    {
        if ($key && (is_numeric($key) || is_string($key)))
            $this->array[$key] = $value;
        else
            $this->array[] = $value;
        return $this;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function addAll(array $array)
    {
        $this->array = $array + $this->array;
        return $this;
    }

    /**
     * @param array $array
     * @param bool $keyArray
     * @return $this
     */
    public function removeAll(array $array, $keyArray = true)
    {
        if ($keyArray)
            $this->array = array_diff_key($this->array, $array);
        else
            $this->array = array_diff($this->array, $array);
        return $this;
    }

    /**
     * @param int|string $index
     * @return $this
     */
    public function remove($index)
    {
        unset($this->array[$index]);
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->array = array();
        return $this;
    }

    /**
     * @param mixed $needle
     * @param bool $strict
     * @return int|string
     */
    public function indexOf($needle, $strict = null)
    {
        return ($index = array_search($needle, $this->array, $strict)) === false ? -1 : $index;
    }

    /**
     * @param mixed $needle
     * @param bool $strict
     * @return int|string
     */
    public function lastIndexOf($needle, $strict = null)
    {
        $arr = new self(array_keys($this->array, $needle, $strict));
        return $arr->get($arr->size() - 1, -1);
    }

    /**
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $v = array_key_exists($key, $this->array) ? $this->array[$key] : $default;
        return Utils::getVal($v, $default);
    }

    /**
     * @return int
     */
    public function size()
    {
        return count($this->array);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->size() == 0;
    }

    /**
     * @return Arrays
     */
    public function getKeys()
    {
        return new self(array_keys($this->array));
    }

    /**
     * @return Arrays
     */
    public function getValues()
    {
        return new self(array_values($this->array));
    }

    /**
     * @param string $arg_separator 参数分隔符
     * @param string $numeric_prefix 参数名为数字时的前缀
     * @return string
     */
    public function toQueryString($arg_separator = '&', $numeric_prefix = null)
    {
        return urldecode(http_build_query($this->array, $numeric_prefix, $arg_separator));
    }

    /**
     * @param string $delimiter
     * @return string
     */
    public function concatByCustom($delimiter = '&')
    {
        $str = new Strings();
        foreach ($this->array as $item) {
            if (is_array($item) || is_object($item))
                $item = (new self($item))->concatByCustom($delimiter);
            $str->concat($item . $delimiter);
        }
        if ($delimiter == '') return $str->getString();
        return $str->substring(0, $str->length() - 1)->getString();
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function callback(callable $callback)
    {
        return $callback($this->array);
    }

    /**
     * @param callable $callback
     */
    public function each(callable $callback)
    {
        try {
            $num = (new ReflectionFunction($callback))->getNumberOfParameters();
        } catch (ReflectionException $e) {
            $num = 0;
        }
        if ($num == 1)
            foreach ($this->array as $v) $callback($v);
        elseif ($num == 2)
            foreach ($this->array as $k => $v) $callback($k, $v);
    }
}