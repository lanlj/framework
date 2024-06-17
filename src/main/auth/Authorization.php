<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 13:47
 */

namespace lanlj\fw\auth;

use lanlj\fw\auth\po\Token;
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class Authorization implements BeanMapping
{
    /**
     * 授权令牌
     * @var Token|null
     */
    private ?Token $token;

    /**
     * Authorization constructor.
     * @param Token|null $token
     */
    public function __construct(Token $token = null)
    {
        $this->token = $token;
    }

    /**
     * @param object|array $args
     * @return self
     */
    public static function mapping($args): self
    {
        if ($args instanceof self) return $args;
        $args = new Arrays($args);
        return new self($args->get('token'));
    }

    /**
     * @return bool
     */
    public function isAuth(): bool
    {
        return !is_null($this->token) && !is_null($this->token->getAccount());
    }

    /**
     * @return Token
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * @param Token $token
     * @return Authorization
     */
    public function setToken(Token $token): Authorization
    {
        $this->token = $token;
        return $this;
    }
}