<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/6/27
 * Time: 11:39
 */

namespace lanlj\fw\model;

class ModelConst
{
    /**
     * @param string $resMsg
     * @return JsonModel
     */
    static function success(string $resMsg): JsonModel
    {
        return new JsonModel('000000', $resMsg);
    }

    /**
     * @param string $resMsg
     * @return JsonModel
     */
    static function fail(string $resMsg = '查询资源信息失败'): JsonModel
    {
        return new JsonModel('999999', $resMsg);
    }
}