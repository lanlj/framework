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
     * @var string|null
     */
    protected ?string $id;

    /**
     * Account constructor.
     * @param string|null $id
     */
    public function __construct(string $id = null)
    {
        $this->id = $id;
    }

    /**
     * @param object|array $args
     * @return self
     */
    public static function mapping($args): self
    {
        if ($args instanceof self) return $args;
        $args = new Arrays($args);
        return new self($args->get('id'));
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
     * @return Account
     */
    public function setId(string $id): Account
    {
        $this->id = $id;
        return $this;
    }
}