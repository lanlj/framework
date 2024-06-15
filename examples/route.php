<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9
 * Time: 21:43
 */

use lanlj\fw\route\{Mapper, Route};

require "./vendor/autoload.php";

$route = Route::getInstance()
    ->setRoute(json_decode(file_get_contents("./src/resources/route.json"), true))
    ->setBaseDir("/framework/examples/")->setNamespace('\lanlj\eg\ctr\%s');
$home = (new Mapper())
    ->setPath(['index.html', 'index.php'])->setName('HomeController');
$default = (new Mapper())->setName('DefaultController');
$test = (new Mapper())
    ->setPath(['test', 'test.do', '~ttt/([^/]+)~'])->setName('TestController');
$route->setHomeMapper($home)->setDefaultMapper($default)->addMapper($test)->run();