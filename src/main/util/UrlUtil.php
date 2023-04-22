<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/24
 * Time: 21:06
 */

namespace lanlj\fw\util;

final class UrlUtil
{
    /**
     * @param string $path
     * @param bool $first
     * @return string
     */
    public static function fixPath($path, $first = false)
    {
        return preg_replace('/[\/]{2,}/', '/', $path, $first ? 1 : -1);
    }
}