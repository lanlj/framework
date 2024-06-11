<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/12
 * Time: 20:10
 */

namespace lanlj\fw\auth\po;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;
use lanlj\fw\util\Utils;

class Token implements BeanMapping
{
    /**
     * ID
     * @var string
     */
    private ?string $id;

    /**
     * @var string
     */
    private ?string $token;

    /**
     * 授权对象
     * @var Account
     * @column("account_id")
     */
    private ?Account $account;

    /**
     * 失效时间
     * @var string
     */
    private ?string $expires;

    /**
     * Token constructor.
     * @param string $id
     * @param string $token
     * @param Account $account
     * @param string $expires
     */
    public function __construct(string $id = null, string $token = null, Account $account = null, string $expires = null)
    {
        $this->id = $id;
        $this->token = $token;
        $this->account = $account;
        $this->expires = $expires;
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
        return new self(
            $values->get('id'),
            $values->get('token'),
            Account::mapping(['id' => $values->get('account_id')]),
            $values->get('expires')
        );
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

    /**
     * @return string
     */
    public function getToken(): string
    {
        if (is_null($this->token)) $this->token = Utils::guid();
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return Account
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getExpires(): string
    {
        if (is_null($this->expires)) $this->expires = time() + 86400;
        return $this->expires;
    }

    /**
     * @param string $expires
     */
    public function setExpires(string $expires)
    {
        $this->expires = $expires;
    }
}