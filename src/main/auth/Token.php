<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/12
 * Time: 20:10
 */

namespace lanlj\fw\auth;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;
use lanlj\fw\util\Utils;

final class Token implements BeanMapping
{
    /**
     * ID
     * @var string
     */
    private $id;

    /**
     * 授权对象
     * @var Account
     */
    private $account;

    /**
     * 失效时间
     * @var string
     */
    private $expires;

    /**
     * Token constructor.
     * @param string $id
     * @param Account $account
     * @param string $expires
     */
    public function __construct($id = null, $expires = null, Account $account = null)
    {
        $this->id = $id;
        $this->expires = $expires;
        $this->account = $account;
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
        return new self(
            $values->get('id'),
            $values->get('expires'),
            Account::mapping(['id' => $values->get('account')])
        );
    }

    /**
     * @return string
     */
    public function getId()
    {
        if (is_null($this->id)) $this->id = Utils::guid();
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Account
     */
    public function getAccount()
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
    public function getExpires()
    {
        if (is_null($this->expires)) $this->expires = time() + 86400;
        return $this->expires;
    }

    /**
     * @param string $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }
}