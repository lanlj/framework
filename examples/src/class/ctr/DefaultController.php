<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/20
 * Time: 17:48
 */

namespace examples\ctr;

use lanlj\ctr\CommController;

class DefaultController extends CommController
{
    /**
     * 逻辑服务
     * @return int
     */
    public function service()
    {
        // TODO: Implement service() method.
        echo "This is a default page.";
        return 1;
    }
}