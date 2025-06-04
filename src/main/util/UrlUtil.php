<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/24
 * Time: 21:06
 */

namespace lanlj\fw\util;

class UrlUtil
{
    /**
     * @param string|null $path
     * @param bool $first
     * @return string
     */
    public static function fixPath(?string $path, bool $first = false): string
    {
        return preg_replace('/[\/]{2,}/', '/', $path, $first ? 1 : -1);
    }

    /**
     * @param string|null $url
     * @return bool
     */
    public static function validateUrl(?string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}