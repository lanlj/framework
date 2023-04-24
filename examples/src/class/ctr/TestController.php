<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/23
 * Time: 21:02
 */

namespace lanlj\eg\ctr;

use lanlj\fw\core\Arrays;
use lanlj\fw\ctr\CommController;

class TestController extends CommController
{
    /**
     * 可用行为集合
     * @var array
     */
    private $acts = array(
        'trb' => 'testRequestBody',
    );

    /**
     * 逻辑服务
     * @return int
     */
    public function service()
    {
        $act = $this->getParam("act");
        $acts = new Arrays($this->acts);
        if ($acts->getKeys()->contains($act)) {
            echo call_user_func_array(array($this, $acts->get($act)), array());
            return 1;
        }
        echo "This is test page." . PHP_EOL;
        echo "Visited " . $this->req->getSession()->getAttribute("views") . " times." . PHP_EOL;
        return 1;
    }

    /**
     * 测试获取请求体数据
     */
    private function testRequestBody()
    {
        ob_end_clean();
        $this->onlyPOST();
        var_dump($_POST);
        echo $this->req->getRequestBody();
    }
}