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
use lanlj\fw\util\DBOUtil;

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
}