<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/23
 * Time: 21:02
 */

namespace lanlj\eg\ctr;

use lanlj\fw\ctr\CommController;

class TestController extends CommController
{
    /**
     * 逻辑服务
     * @return int
     */
    public function service()
    {
        echo "This is test page." . PHP_EOL;
        echo "Visited " . $this->req->getSession()->getAttribute("views") . " times." . PHP_EOL;
        return 1;
    }
}