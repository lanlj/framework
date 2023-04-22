<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/20
 * Time: 17:48
 */

namespace lanlj\eg\ctr;

use lanlj\fw\ctr\CommController;

class DefaultController extends CommController
{
    /**
     * 逻辑服务
     * @return int
     */
    public function service()
    {
        echo "This is a default page.";
        return 1;
    }
}