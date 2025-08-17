<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/8/16
 * Time: 16:33
 */

namespace lanlj\eg\po;

use stdClass;

class EntitySon extends EntityFa
{
    /**
     * @var stdClass
     */
    private stdClass $prop5;

    /**
     * @var string
     * @column('prop_6')
     */
    private string $prop6 = 'prop6';

    /**
     * EntitySon constructor.
     */
    public function __construct()
    {
        $this->prop5 = (object)['a' => 'a', 'b' => (object)['c' => 'c', 'd' => 'd']];
    }

    /**
     * @inheritDoc
     * @see ArrayUtil::toArray()
     */
    public function toArray(bool $onlyPublic = true, bool $all = false, bool $db = false, ...$args): array
    {
        $vars = get_object_vars($this);
        if ($db) $vars = array_combine(['prop5', 'prop_6'], $vars);
        return parent::toArray($onlyPublic, $all, $db, ...$args) + $vars;
    }
}