<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/8/16
 * Time: 16:32
 */

namespace lanlj\eg\po;

use lanlj\fw\bean\BeanArray;
use lanlj\fw\util\ArrayUtil;

class EntityPa implements BeanArray
{
    /**
     * @var string
     */
    private string $prop1 = 'prop1';

    /**
     * @var string
     * @column('prop_2')
     */
    private string $prop2 = 'prop2';

    /**
     * @inheritDoc
     * @see ArrayUtil::toArray()
     */
    public function toArray(bool $onlyPublic = true, bool $all = false, bool $db = false, ...$args): array
    {
        $vars = get_object_vars($this);
        if ($db == false) return $vars;
        return array_combine(['prop1', 'prop_2'], $vars);
    }
}