<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28
 * Time: 15:02
 */

namespace lanlj\fw\auth\po;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class Account implements BeanMapping
{
    /**
     * ID
     * @var string
     */
    protected ?string $id;

    /**
     * Account constructor.
     * @param string $id
     */
    public function __construct(string $id = null)
    {
        $this->id = $id;
    }

    /**
     * @param object|array $values
     * @return self
     */
    public static function mapping($values): self
    {
        if ($values instanceof self)
            return $values;
        $values = new Arrays($values);
        return new self($values->get('id'));
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }
}