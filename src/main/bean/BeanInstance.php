<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/14
 * Time: 16:51
 */

namespace lanlj\fw\bean;

interface BeanInstance
{
    /**
     * @param array ...$_
     * @return self
     */
    public static function newInstance(...$_);
}