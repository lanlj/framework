<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/11
 * Time: 4:02
 */

namespace lanlj\fw\repo;

class TokenRepo extends Repository
{
    /**
     * @var string
     */
    private static string $table = "lj_token";

    /**
     * @param string $table
     */
    public static function setTable(string $table = "lj_token"): void
    {
        self::$table = $table;
    }

    /**
     * @inheritDoc
     */
    public function initDAO(): void
    {
        parent::setDAO(self::$table);
    }
}