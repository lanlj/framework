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
     * 可用行为集合
     * @var array
     */
    private array $acts = array(
        'trb' => 'testRequestBody',
    );

    /**
     * 逻辑服务
     * @return int
     */
    public function service(): int
    {
        echo "This is test page." . PHP_EOL;
        echo "Visited " . $this->req->getSession()->getAttribute("views") . " times." . PHP_EOL;
        return $this->quickService($this->acts, $this->getParam("act"));
    }

    /**
     * 测试获取请求体数据
     * @return int
     */
    protected function testRequestBody(): int
    {
        ob_end_clean();
        $this->onlyPOST();
        var_dump($_POST);
        echo $this->req->getRequestBody();
        return 200;
    }
}