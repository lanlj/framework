<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/7/21
 * Time: 18:29
 */

namespace lanlj\fw\bean;

interface BeanArray
{
    /**
     * @param bool $onlyPublic
     * @param bool $all
     * @param bool $db
     * @param mixed ...$args
     * @return array
     */
    public function toArray(bool $onlyPublic = true, bool $all = false, bool $db = false, ...$args): array;
}