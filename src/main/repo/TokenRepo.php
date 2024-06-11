<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/11
 * Time: 4:02
 */

namespace lanlj\fw\repo;

use lanlj\fw\auth\po\Token;
use lanlj\fw\util\ArrayUtil;

class TokenRepo extends Repository
{
    /**
     * @var string
     */
    private string $table = "lj_token";


    /**
     * 新增条目
     * @param Token $token
     * @return bool
     */
    public function insert(Token $token): bool
    {
        $data = ArrayUtil::toArray($token, false, true, true);
        $data['account_id'] = $data['account_id']['id'];
        return self::$dboUtil->insert($this->table, $data);
    }

    /**
     * 通过 accountId 查询单条目
     * @param string $accountId
     * @return object
     */
    public function getOneByAccountId(?string $accountId): ?object
    {
        $sql = "SELECT id, token, expires FROM $this->table WHERE account_id = '$accountId' ORDER BY expires DESC LIMIT 0, 1;";
        return self::$dboUtil->getOne($sql);
    }

    /**
     * 通过 token 查询单条目
     * @param string $token
     * @return object
     */
    public function getOneByToken(?string $token): ?object
    {
        return self::$dboUtil->getOne("SELECT * FROM $this->table WHERE token = '$token';");
    }

    /**
     * @param string $table
     */
    public function setTable(string $table = "lj_token"): void
    {
        $this->table = $table;
    }
}