<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/7/21
 * Time: 18:29
 */

namespace lanlj\fw\bean;

interface ArrayBean
{
    /**
     * @param mixed ...$args
     * @return array
     */
    public function toArray(...$args): array;
}