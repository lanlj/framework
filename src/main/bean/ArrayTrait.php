<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/7/21
 * Time: 20:30
 */

namespace lanlj\fw\bean;

trait ArrayTrait
{
    /**
     * @param mixed ...$args
     * @return array
     */
    public function toArray(...$args): array
    {
        return get_object_vars($this);
    }
}