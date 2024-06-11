<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/9
 * Time: 14:23
 */

namespace lanlj\fw\repo;

use ezsql\ezsqlModel;
use lanlj\fw\core\{Arrays, Strings};
use lanlj\fw\util\{ArrayUtil, DBOUtil};

abstract class Repository
{
    /**
     * @var ezsqlModel
     */
    protected static ezsqlModel $dbo;

    /**
     * @var DBOUtil
     */
    protected static DBOUtil $dboUtil;


    /**
     * @param ezsqlModel $dbo
     */
    public static function initialize(ezsqlModel $dbo)
    {
        self::$dbo = $dbo;
        self::$dbo->prepareOn();
        self::$dboUtil = new DBOUtil(self::$dbo);
    }

    /**
     * @param string $sql
     * @param object $entity
     * @param Credentials[] $credentialsList
     * @param string ...$ignoredCols
     * @return string
     */
    protected function buildCredentialsSQL(string $sql, object $entity = null, array $credentialsList = null, ...$ignoredCols): string
    {
        if (is_null($entity)) $entity = [];
        if (is_null($credentialsList)) $credentialsList = [];
        $data = ArrayUtil::toArray($entity, false, false, true);
        $sql = new Strings($sql);
        $ignoredCols = new Arrays($ignoredCols);
        foreach ($credentialsList as $item) {
            if ($ignoredCols->contains($item->getCol())) continue;
            $sql->concat(sprintf($item->getLabel(), $data[$item->getCol()]));
        }
        return $sql->replaceFirst(['/1=1 AND/', '/1=1 OR/'], '')->concat(";")->getString();
    }
}