<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/16
 * Time: 14:24
 */

namespace lanlj\fw\bean;

use lanlj\fw\util\BeanUtil;

class BeanDemo implements BeanMapping, BeanInstance
{
    /**
     * @var object
     */
    public static $object;

    /**
     * @param array ...$_
     * @return self
     */
    public static function newInstance(...$_)
    {
        return self::mapping($_[0]);
    }

    /**
     * @param object|array $values
     * @return $this
     */
    public static function mapping($values)
    {
        if (is_object(self::$object))
            self::$object = BeanUtil::populate($values, get_class(self::$object));
        return new self();
    }
}