<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/13
 * Time: 11:46
 */

namespace lanlj\fw\filter;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\http\{Request, Response};

final class CORSFilter implements Filter, BeanMapping
{
    /**
     * @param object|array $args
     * @return $this
     */
    public static function mapping($args): self
    {
        return $args instanceof self ? $args : new self();
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function doFilter(Request $request, Response $response): void
    {
        $response->setHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET,POST,HEAD,PUT,DELETE,OPTIONS',
            'Access-Control-Max-Age' => '3600',
            'Access-Control-Allow-Headers' => 'Accept,Origin,X-Requested-With,Content-Type,X-Auth-Token',
            'Access-Control-Allow-Credentials' => 'true'
        ]); // 响应头设置
    }
}