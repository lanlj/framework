<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/8/16
 * Time: 16:33
 */

namespace lanlj\eg\po;

class EntityFa extends EntityPa
{
    /**
     * @var string
     */
    private string $prop3 = 'prop3';

    /**
     * @var string
     */
    private string $prop4 = 'prop4';

    /**
     * @inheritDoc
     * @see ArrayUtil::toArray()
     */
    public function toArray(bool $onlyPublic = true, bool $all = false, bool $db = false, ...$args): array
    {
        return parent::toArray($onlyPublic, $all, $db, ...$args) + get_object_vars($this);
    }
}