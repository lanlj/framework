<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/6/27
 * Time: 3:53
 */

namespace lanlj\fw\util;

use lanlj\fw\model\{JsonModel, PageModel};
use lanlj\fw\page\PageInfo;

class PageUtils
{
    /**
     * @param JsonModel $jsonModel
     * @param PageInfo $pageInfo
     * @return PageModel
     */
    public static function convert(JsonModel $jsonModel, PageInfo $pageInfo): PageModel
    {
        return (new PageModel($jsonModel->getResCode(), $jsonModel->getResMsg()))
            ->setPageNum($pageInfo->getPageNum())->setPageSize($pageInfo->getPageSize())
            ->setSize($pageInfo->getSize())->setTotal($pageInfo->getTotal())->setPages($pageInfo->getPages())
            ->setBusiDataResp($pageInfo->getList());
    }
}