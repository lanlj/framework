<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/1
 * Time: 12:04
 */

namespace lanlj\fw\http\storage;

use lanlj\fw\base\Arrays;

final class Session
{
    /**
     * @var self
     */
    private static self $_instance;

    /**
     * Session constructor.
     * @param int $cacheExpire
     */
    private function __construct(int $cacheExpire = 180)
    {
        session_cache_expire($cacheExpire);
        session_start();
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$_instance ?? self::$_instance = new self();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute(string $name)
    {
        return (new Arrays($_SESSION))->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute(string $name, $value): void
    {
        $_SESSION = (new Arrays($_SESSION))->add($value, $name)->getArray();
    }

    /**
     * @return Arrays
     */
    public function getAttributeNames(): Arrays
    {
        return (new Arrays($_SESSION))->getKeys();
    }

    /**
     * @param string $name
     */
    public function removeAttribute(string $name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * @return int
     */
    public function getCacheExpire(): int
    {
        return session_cache_expire();
    }

    /**
     * @param int $min
     */
    public function setCacheExpire(int $min)
    {
        session_destroy();
        self::$_instance = new self($min);
    }

    /**
     * 清空所有会话数据
     */
    public function invalidate(): void
    {
        session_unset();
    }

    private function __clone()
    {
    }
}