<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/20
 * Time: 16:33
 */

namespace lanlj\fw\ctr;

interface Controller
{
    /**
     * 逻辑服务
     * @return int
     */
    public function service(): int;
}