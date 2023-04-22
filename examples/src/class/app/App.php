<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/13
 * Time: 20:22
 */

namespace lanlj\eg\app;

use lanlj\fw\app\Application;

class App extends Application
{
    public $str = 'string';

    /**
     * App constructor.
     */
    protected function __construct()
    {
        parent::__construct();
        echo __METHOD__ . PHP_EOL;
    }

    /**
     * 启动程序
     */
    public function startup()
    {
        parent::startup();
        echo __METHOD__ . PHP_EOL;
    }
}