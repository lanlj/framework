<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 13:47
 */

namespace lanlj\fw\auth;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

final class Authorization implements BeanMapping
{
    /**
     * 授权令牌
     * @var Token
     */
    private $token;

    /**
     * Authorization constructor.
     * @param Token $token
     */
    public function __construct(Token $token = null)
    {
        $this->token = $token;
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
        return new self($values->get('token'));
    }

    /**
     * @return bool
     */
    public function isAuth()
    {
        return !is_null($this->token) && !is_null($this->token->getAccount());
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }
}