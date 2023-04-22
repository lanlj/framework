<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 23:44
 */

namespace lanlj\fw\core;

use lanlj\fw\util\StringUtil;

final class Strings
{
    const GBK = "GBK";
    const BIG5 = "BIG5";
    const UTF8 = "UTF-8";
    const GB2312 = "GB2312";

    /**
     * @var string
     */
    private $string;

    /**
     * Strings constructor.
     * @param string $string
     */
    public function __construct($string = '')
    {
        if (!is_string($string)) $string = StringUtil::toString($string);
        $this->string = $string;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getString();
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * 是否开始于
     * @param string $needle
     * @param bool $i
     * @return bool
     */
    public function startsWith($needle, $i = true)
    {
        if (!$i) return strpos($this->string, $needle) === 0;
        return stripos($this->string, $needle) === 0;
    }

    /**
     * 是否结束于
     * @param string $needle
     * @param bool $i
     * @return bool
     */
    public function endsWith($needle, $i = true)
    {
        if (!$i) return ($pos = strrpos($this->string, $needle)) !== false && $pos == strlen($this->string) - strlen($needle);
        return ($pos = strripos($this->string, $needle)) !== false && $pos == strlen($this->string) - strlen($needle);
    }

    /**
     * 是否包含
     * @param string $needle
     * @param bool $i
     * @return bool
     */
    public function contains($needle, $i = true)
    {
        if (!$i) return stripos($this->string, $needle) !== false;
        return strpos($this->string, $needle) !== false;
    }

    /**
     * 连接字符串
     * @param string $string
     * @return $this
     */
    public function concat($string)
    {
        $this->string .= $string;
        return $this;
    }

    /**
     * 是否为空
     * @return bool
     */
    public function isEmpty()
    {
        return $this->string == "";
    }

    /**
     * 去除前后空格
     * @return Strings
     */
    public function trim()
    {
        return new self(trim($this->string));
    }

    /**
     * 字符串替换
     * @param mixed $search
     * @param mixed $replacement
     * @return Strings
     */
    public function replace($search, $replacement)
    {
        return new self(str_replace($search, $replacement, $this->string));
    }

    /**
     * 字符串正则替换所有匹配项
     * @param string $pattern
     * @param mixed $replacement
     * @return Strings
     */
    public function replaceAll($pattern, $replacement)
    {
        return new self(preg_replace($pattern, $replacement, $this->string));
    }

    /**
     * 字符串正则替换最后匹配项
     * @param string $pattern
     * @param mixed $replacement
     * @return $this|Strings
     */
    public function replaceLast($pattern, $replacement)
    {
        if ($this->matches($pattern, $matches, true)) {
            $matches = new self($matches[0][count($matches[0]) - 1]);
            return new self(substr_replace($this->string, $replacement, $this->lastIndexOf($matches->string), $matches->length()));
        }
        return $this;
    }

    /**
     * 是否正则匹配
     * @param string $pattern
     * @param array $matches
     * @param bool $matchAll
     * @return int
     */
    public function matches($pattern, array &$matches = null, $matchAll = false)
    {
        if (!$matchAll)
            return preg_match($pattern, $this->string, $matches);
        return preg_match_all($pattern, $this->string, $matches);
    }

    /**
     * 查找字符串最后位置
     * @param string $needle
     * @param int $offset
     * @return int
     */
    public function lastIndexOf($needle, $offset = null)
    {
        return mb_strrpos($this->string, $needle, $offset, $this->getEncoding());
    }

    /**
     * 获取字符串编码
     * @return string
     */
    public function getEncoding()
    {
        return mb_detect_encoding($this->string, mb_detect_order());
    }

    /**
     * 获取字符串长度
     * @return int
     */
    public function length()
    {
        return mb_strlen($this->string, $this->getEncoding());
    }

    /**
     * 转换字符串编码
     * @param string $to_encoding
     * @param string $from_encoding
     * @param bool $ignore
     * @return $this|Strings
     */
    public function convertEncoding($to_encoding = self::UTF8, $from_encoding = null, $ignore = false)
    {
        $to_encoding = (new self($to_encoding))->toUpperCase()->string;
        $from_encoding = is_string($from_encoding) ? $from_encoding : $this->getEncoding();
        if ($ignore || $from_encoding != $to_encoding) {
            return new self(mb_convert_encoding($this->string, $to_encoding, $from_encoding));
        }
        return $this;
    }

    /**
     * 英文字符串转大写
     * @return Strings
     */
    public function toUpperCase()
    {
        return new self(mb_convert_case($this->string, MB_CASE_UPPER, $this->getEncoding()));
    }

    /**
     * 字符串反转
     * @return Strings
     */
    public function reverse()
    {
        return new self(strrev($this->string));
    }

    /**
     * 字符串正则替换第一匹配项
     * @param string $pattern
     * @param mixed $replacement
     * @return Strings
     */
    public function replaceFirst($pattern, $replacement)
    {
        return new self(preg_replace($pattern, $replacement, $this->string, 1));
    }

    /**
     * 字符串切割为数组
     * @param string $pattern
     * @param bool $regex
     * @param $limit
     * @return array
     */
    public function split($pattern, $regex = false, $limit = 'null')
    {
        if (!$regex)
            if ($limit == 'null')
                return explode($pattern, $this->string);
            else
                return explode($pattern, $this->string, $limit);
        return preg_split($pattern, $this->string, $limit == 'null' ? -1 : $limit);
    }

    /**
     * 字符串截取
     * @param int $start
     * @param int $length
     * @return Strings
     */
    public function substring($start, $length = null)
    {
        return new self(mb_substr($this->string, $start, $length, $this->getEncoding()));
    }

    /**
     * 查找字符串首在位置
     * @param string $needle
     * @param int $offset
     * @return bool|int
     */
    public function indexOf($needle, $offset = null)
    {
        return mb_strpos($this->string, $needle, $offset, $this->getEncoding());
    }

    /**
     * 字符串是否相等（区分大小写）
     * @param string $anotherString
     * @return bool
     */
    public function equals($anotherString)
    {
        return $anotherString == $this->string;
    }

    /**
     * 字符串是否相等（不区分大小写）
     * @param string $anotherString
     * @return bool
     */
    public function equalsIgnoreCase($anotherString)
    {
        return (new self($anotherString))->toLowerCase()->string == $this->toLowerCase()->string;
    }

    /**
     * 英文字符串转小写
     * @return Strings
     */
    public function toLowerCase()
    {
        return new self(mb_convert_case($this->string, MB_CASE_LOWER, $this->getEncoding()));
    }
}