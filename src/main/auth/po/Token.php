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
use lanlj\fw\util\ArrayUtil;
use lanlj\fw\util\Utils;

class Token implements BeanMapping
{
    /**
     * ID
     * @var string|null
     */
    private ?string $id;

    /**
     * @var string|null
     */
    private ?string $token;

    /**
     * 授权对象
     * @var Account|null
     * @column("account_id")
     */
    private ?Account $account;

    /**
     * 失效时间
     * @var string|null
     */
    private ?string $expires;

    /**
     * Token constructor.
     * @param string|null $id
     * @param string|null $token
     * @param Account|null $account
     * @param string|null $expires
     */
    public function __construct(string $id = null, string $token = null, Account $account = null, string $expires = null)
    {
        $this->id = $id;
        $this->token = $token;
        $this->account = $account;
        $this->expires = $expires;
    }

    /**
     * @param object|array $args
     * @return self
     */
    public static function mapping($args): self
    {
        if ($args instanceof self) return $args;
        $args = new Arrays($args);
        return new self(
            $args->get('id'),
            $args->get('token'),
            Account::mapping(['id' => $args->get('account_id')]),
            $args->get('expires')
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
     * @return Token
     */
    public function setId(string $id): Token
    {
        $this->id = $id;
        return $this;
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
     * @return Token
     */
    public function setToken(string $token): Token
    {
        $this->token = $token;
        return $this;
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
     * @return Token
     */
    public function setAccount(Account $account): Token
    {
        $this->account = $account;
        return $this;
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
     * @return Token
     */
    public function setExpires(string $expires): Token
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = ArrayUtil::toArray($this, false, true, true);
        $data['account_id'] = $data['account_id']['id'];
        return $data;
    }
}