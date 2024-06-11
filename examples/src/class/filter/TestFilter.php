<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/29
 * Time: 9:45
 */

namespace lanlj\eg\filter;

use lanlj\fw\filter\Filter;
use lanlj\fw\http\{Request, Response};
use lanlj\fw\util\Utils;

class TestFilter implements Filter
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function doFilter(Request $request, Response $response): void
    {
        echo __METHOD__ . PHP_EOL;

        $session = $request->getSession();
        $views = $session->getAttribute("views");
        $views = Utils::getVal($views, 0);
        $session->setAttribute("views", ++$views);
    }
}