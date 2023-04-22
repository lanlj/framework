<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/1
 * Time: 12:04
 */

namespace lanlj\fw\http\storage;

use lanlj\fw\core\Arrays;

final class Session
{
    /**
     * @var Session
     */
    private static $_instance = null;

    /**
     * Session constructor.
     * @param int $cacheExpire
     */
    private function __construct($cacheExpire = 180)
    {
        session_cache_expire($cacheExpire);
        session_start();
    }

    /**
     * @return Session
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        return (new Arrays($_SESSION))->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        $_SESSION = (new Arrays($_SESSION))->add($value, $name)->getArray();
    }

    /**
     * @return Arrays
     */
    public function getAttributeNames()
    {
        return (new Arrays($_SESSION))->getKeys();
    }

    /**
     * @param string $name
     */
    public function removeAttribute($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * @return int
     */
    public function getCacheExpire()
    {
        return session_cache_expire();
    }

    /**
     * @param int $min
     */
    public function setCacheExpire($min)
    {
        session_destroy();
        self::$_instance = new self($min);
    }

    /**
     * 清空所有会话数据
     */
    public function invalidate()
    {
        session_unset();
    }

    private function __clone()
    {
    }
}