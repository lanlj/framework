<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/25
 * Time: 23:18
 */

namespace examples\ctr;

use lanlj\ctr\CommController;

class HomeController extends CommController
{
    /**
     * 逻辑服务
     * @return int
     */
    public function service()
    {
        // TODO: Implement service() method.
        echo "This is homepage.";
        return 1;
    }
}