<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/29
 * Time: 1:02
 */

namespace lanlj\fw\core;

use lanlj\fw\util\{ArrayUtil, JsonUtil, Utils};
use ReflectionException;
use ReflectionFunction;

class Arrays
{
    /**
     * @var array
     */
    private array $array;

    /**
     * Arrays constructor.
     * @param array|object $array
     */
    public function __construct($array = array())
    {
        $this->array = is_array($array) ? $array : ArrayUtil::toArray($array);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return JsonUtil::toJsonString($this->array);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @param string $rootNode
     * @return string
     */
    public function toXML(string $rootNode = "xml"): string
    {
        return ArrayUtil::toXML($this->array, null, null, [ArrayUtil::XML_ROOT => $rootNode]);
    }

    /**
     * @param mixed $needle
     * @param bool $strict
     * @param bool $regex
     * @return bool
     */
    public function contains($needle, bool $strict = false, bool $regex = false): bool
    {
        if (!$regex) return in_array($needle, $this->array, $strict);
        foreach ($this->array as $value) {
            if (preg_match($needle, $value)) return true;
        }
        return false;
    }

    /**
     * @param mixed $value
     * @param string|null $key
     * @return $this
     */
    public function add($value, string $key = null): self
    {
        is_null($key) ? $this->array[] = $value : $this->array[$key] = $value;
        return $this;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function addAll(array $array): self
    {
        $this->array = $array + $this->array;
        return $this;
    }

    /**
     * @param array $array
     * @param bool $keyArray
     * @return $this
     */
    public function removeAll(array $array, bool $keyArray = true): self
    {
        if ($keyArray) $this->array = array_diff_key($this->array, $array);
        else $this->array = array_diff($this->array, $array);
        return $this;
    }

    /**
     * @param string $index
     * @return $this
     */
    public function remove(string $index): self
    {
        unset($this->array[$index]);
        return $this;
    }

    /**
     * @return $this
     */
    public function clear(): self
    {
        $this->array = array();
        return $this;
    }

    /**
     * @param mixed $needle
     * @param bool $strict
     * @return int|string
     */
    public function indexOf($needle, bool $strict = null)
    {
        return ($index = array_search($needle, $this->array, $strict)) === false ? -1 : $index;
    }

    /**
     * @param mixed $needle
     * @param bool $strict
     * @return int|string
     */
    public function lastIndexOf($needle, bool $strict = null)
    {
        $arr = new self(array_keys($this->array, $needle, $strict));
        return $arr->get($arr->size() - 1, -1);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $v = array_key_exists($key, $this->array) ? $this->array[$key] : $default;
        return Utils::getVal($v, $default);
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return count($this->array);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size() == 0;
    }

    /**
     * @return Arrays
     */
    public function getKeys(): self
    {
        return new self(array_keys($this->array));
    }

    /**
     * @return Arrays
     */
    public function getValues(): self
    {
        return new self(array_values($this->array));
    }

    /**
     * @param string $arg_separator 参数分隔符
     * @param string $numeric_prefix 参数名为数字时的前缀
     * @return string
     */
    public function toQueryString(string $arg_separator = '&', string $numeric_prefix = ""): string
    {
        return urldecode(http_build_query($this->array, $numeric_prefix, $arg_separator));
    }

    /**
     * @param string $delimiter
     * @return string
     */
    public function concatByCustom(string $delimiter = '&'): string
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