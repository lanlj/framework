<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/6
 * Time: 18:11
 */

namespace lanlj\fw\bean;

interface BeanMapping
{
    /**
     * @param object|array $values
     * @return self
     */
    public static function mapping($values);
}