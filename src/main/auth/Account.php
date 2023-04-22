<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28
 * Time: 15:02
 */

namespace lanlj\fw\auth;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class Account implements BeanMapping
{
    /**
     * ID
     * @var string
     */
    protected $id;

    /**
     * Account constructor.
     * @param string $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @param object|array $values
     * @return $this
     */
    public static function mapping($values)
    {
        if ($values instanceof self)
            return $values;
        $values = new Arrays($values);
        return new self($values->get('id'));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}