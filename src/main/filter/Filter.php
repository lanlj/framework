<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/16
 * Time: 15:06
 */

namespace lanlj\filter;

use lanlj\http\Request;
use lanlj\http\Response;

interface Filter
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function doFilter(Request $request, Response $response);
}