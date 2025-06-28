<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/13
 * Time: 20:39
 */

namespace lanlj\fw\ctr;

use lanlj\fw\app\Application;
use lanlj\fw\http\{Request, Response};
use lanlj\fw\http\cURL\{Curl, UniCurl};

abstract class CommController implements Controller
{
    /**
     * 对象实例
     * @var self
     */
    private static self $_instance;

    /**
     * @var Request
     */
    protected Request $req;

    /**
     * @var Response
     */
    protected Response $resp;

    /**
     * CommController constructor.
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * 初始化配置
     * @return void
     */
    protected function init(): void
    {
        $this->req = Application::getRequest();
        $this->resp = Application::getResponse();

        $this->resp->setContentType('text/html; charset=utf-8'); // 默认类型及编码
    }

    /**
     * 获取对象实例
     * @return self
     */
    public static final function getInstance(): self
    {
        return self::$_instance ?? self::$_instance = new static();
    }

    /**
     * 获取Curl实例
     * @param string $url
     * @return UniCurl
     */
    protected final function getCurl(string $url): UniCurl
    {
        return new UniCurl(
            (new Curl())->setUrl($url)
                ->setDefaultTimeout()
                ->setDefaultUserAgent()
                ->setAutoGenerateReferer(true)
        );
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected final function getParam(string $name, $default = null)
    {
        return $this->req->getParam($name, $default);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return array
     */
    protected final function getParams(string $name, $default = null): array
    {
        return $this->req->getParams($name, $default);
    }

    /**
     * 只允许GET请求
     * @param bool $output
     * @return string|null
     */
    protected function onlyGET(bool $output = true): ?string
    {
        return $this->req->requestMethods($output, Request::GET);
    }

    /**
     * 只允许POST请求
     * @param bool $output
     * @return string|null
     */
    protected function onlyPOST(bool $output = true): ?string
    {
        return $this->req->requestMethods($output, Request::POST);
    }

    private function __clone()
    {
    }
}