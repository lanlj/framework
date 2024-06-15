<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 23:44
 */

namespace lanlj\fw\core;

class Strings
{
    const GBK = "GBK";
    const BIG5 = "BIG5";
    const UTF8 = "UTF-8";
    const GB2312 = "GB2312";

    /**
     * @var string
     */
    private string $string;

    /**
     * Strings constructor.
     * @param string|null $string
     */
    public function __construct(?string $string = '')
    {
        $this->string = $string ?? '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getString();
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * 是否开始于
     * @param string $needle
     * @param bool $i
     * @return bool
     */
    public function startsWith(string $needle, bool $i = true): bool
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
    public function endsWith(string $needle, bool $i = true): bool
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
    public function contains(string $needle, bool $i = true): bool
    {
        if (!$i) return stripos($this->string, $needle) !== false;
        return strpos($this->string, $needle) !== false;
    }

    /**
     * 连接字符串
     * @param string $string
     * @return $this
     */
    public function concat(string $string): self
    {
        $this->string .= $string;
        return $this;
    }

    /**
     * 是否为空
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->string == "";
    }

    /**
     * 去除前后空格
     * @return self
     */
    public function trim(): self
    {
        return new self(trim($this->string));
    }

    /**
     * 字符串替换
     * @param array|string $search
     * @param array|string $replace
     * @return self
     */
    public function replace($search, $replace): self
    {
        return new self(str_replace($search, $replace, $this->string));
    }

    /**
     * 字符串正则替换所有匹配项
     * @param array|string $pattern
     * @param array|string $replacement
     * @return self
     */
    public function replaceAll($pattern, $replacement): self
    {
        return new self(preg_replace($pattern, $replacement, $this->string));
    }

    /**
     * 字符串正则替换最后匹配项
     * @param string $pattern
     * @param mixed $replacement
     * @return $this
     */
    public function replaceLast(string $pattern, $replacement): self
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
     * @param $matches
     * @param bool $matchAll
     * @return bool
     */
    public function matches(string $pattern, &$matches, bool $matchAll = false): bool
    {
        if (!$matchAll) return preg_match($pattern, $this->string, $matches);
        return preg_match_all($pattern, $this->string, $matches);
    }

    /**
     * 查找字符串最后位置
     * @param string $needle
     * @param int $offset
     * @return int
     */
    public function lastIndexOf(string $needle, int $offset = 0): int
    {
        return mb_strrpos($this->string, $needle, $offset, $this->getEncoding());
    }

    /**
     * 获取字符串编码
     * @return string
     */
    public function getEncoding(): string
    {
        return mb_detect_encoding($this->string, mb_detect_order());
    }

    /**
     * 获取字符串长度
     * @return int
     */
    public function length(): int
    {
        return mb_strlen($this->string, $this->getEncoding());
    }

    /**
     * 转换字符串编码
     * @param string $to_encoding
     * @param string|null $from_encoding
     * @param bool $ignore
     * @return $this
     */
    public function convertEncoding(string $to_encoding = self::UTF8, string $from_encoding = null, bool $ignore = false): self
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
     * @return self
     */
    public function toUpperCase(): self
    {
        return new self(mb_convert_case($this->string, MB_CASE_UPPER, $this->getEncoding()));
    }

    /**
     * 字符串反转
     * @return self
     */
    public function reverse(): self
    {
        return new self(strrev($this->string));
    }

    /**
     * 字符串正则替换第一匹配项
     * @param array|string $pattern
     * @param array|string $replacement
     * @return self
     */
    public function replaceFirst($pattern, $replacement): self
    {
        return new self(preg_replace($pattern, $replacement, $this->string, 1));
    }

    /**
     * 字符串切割为数组
     * @param string $pattern
     * @param bool $regex
     * @param int $limit
     * @return array
     */
    public function split(string $pattern, bool $regex = false, int $limit = -1): array
    {
        if (!$regex)
            return $limit == -1 ? explode($pattern, $this->string) : explode($pattern, $this->string, $limit);
        return preg_split($pattern, $this->string, $limit);
    }

    /**
     * 字符串截取
     * @param int $start
     * @param int|null $length
     * @return self
     */
    public function substring(int $start, int $length = null): self
    {
        return new self(mb_substr($this->string, $start, $length, $this->getEncoding()));
    }

    /**
     * 查找字符串首在位置
     * @param string $needle
     * @param int $offset
     * @return int|bool
     */
    public function indexOf(string $needle, int $offset = 0)
    {
        return mb_strpos($this->string, $needle, $offset, $this->getEncoding());
    }

    /**
     * 字符串是否相等（区分大小写）
     * @param string|null $anotherString
     * @return bool
     */
    public function equals(?string $anotherString): bool
    {
        return $anotherString == $this->string;
    }

    /**
     * 字符串是否相等（不区分大小写）
     * @param string|null $anotherString
     * @return bool
     */
    public function equalsIgnoreCase(?string $anotherString): bool
    {
        return (new self($anotherString))->toLowerCase()->string == $this->toLowerCase()->string;
    }

    /**
     * 英文字符串转小写
     * @return self
     */
    public function toLowerCase(): self
    {
        return new self(mb_convert_case($this->string, MB_CASE_LOWER, $this->getEncoding()));
    }
}