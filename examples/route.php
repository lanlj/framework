<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9
 * Time: 21:43
 */
require "./vendor/autoload.php";

$route = \lanlj\route\Route::getInstance()
    ->setRoute(json_decode(file_get_contents("./src/resources/route.json"), true))
    ->setBaseDir("/framework/examples/")->setNamespace('\examples\ctr\%s');
$home = (new \lanlj\route\Mapper())
    ->setPath(['index.html', 'index.php'])->setName('HomeController');
$default = (new \lanlj\route\Mapper())->setName('DefaultController');
$test = (new \lanlj\route\Mapper())
    ->setPath(['test', 'test.do', '~ttt/([^/]+)~'])->setName('TestController');
$route->setHomeMapper($home)->setDefaultMapper($default)->addMapper($test)->run();