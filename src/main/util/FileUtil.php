<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/18
 * Time: 17:40
 */

namespace lanlj\fw\util;

use lanlj\fw\http\cURL\{Curl, UniCurl};

class FileUtil
{
    /**
     * 创建多级文件夹/目录
     * @param string $dir
     * @param int $mode
     * @return bool
     */
    public static function mkDirs(string $dir, int $mode = 0777): bool
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!self::mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }

    /**
     * 获取文件名（支持中文）
     * @param string|null $path
     * @return string|null
     */
    public static function basename(?string $path): ?string
    {
        $arr = explode("/", $path);
        return array_pop($arr);
    }

    /**
     * 判断远程文件是否存在
     * @param string $url
     * @param bool $useCurl
     * @return bool
     */
    public static function remoteFileExists(string $url, bool $useCurl = false): bool
    {
        if (!$useCurl) return get_headers($url)[0] == 'HTTP/1.1 200 OK';
        $packet = (new UniCurl(
            (new Curl())->setUrl($url)
                ->setDefaultTimeout()
                ->setDefaultUserAgent()
                ->setAutoGenerateReferer(true)
                ->setFollowLocation(true)
        ))->head();
        return $packet->getErrNo() == 0 && $packet->getStatusCode() == 'HTTP/1.1 200 OK';
    }
}