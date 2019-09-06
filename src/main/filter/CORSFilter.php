<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/1/13
 * Time: 11:46
 */

namespace lanlj\filter;

use lanlj\bean\BeanMapping;
use lanlj\http\Request;
use lanlj\http\Response;

final class CORSFilter implements Filter, BeanMapping
{
    /**
     * @param object|array $values
     * @return $this
     */
    public static function mapping($values)
    {
        if ($values instanceof self) return $values;
//        $values = new Arrays($values);
        return new self();
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function doFilter(Request $request, Response $response)
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