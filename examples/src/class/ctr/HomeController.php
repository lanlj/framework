<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/25
 * Time: 23:18
 */

namespace lanlj\eg\ctr;

use lanlj\fw\ctr\CommController;

class HomeController extends CommController
{
    /**
     * 逻辑服务
     * @return int
     */
    public function service()
    {
        echo "This is homepage.";
        return 1;
    }
}