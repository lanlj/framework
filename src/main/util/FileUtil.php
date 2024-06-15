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
     * @param bool $get_headers
     * @return bool
     */
    public static function remoteFileExists(string $url, bool $get_headers = false): bool
    {
        if ($get_headers)
            return get_headers($url)[0] == 'HTTP/1.1 200 OK';
        $r = (new UniCurl(
            (new Curl())->setUrl($url)
                ->setDefaultTimeout()
                ->setDefaultUserAgent()
                ->setReferer('https://www.baidu.com')
                ->setOpt(CURLOPT_FOLLOWLOCATION, true)
        ))->head();
        return !isset($r['err_no']) && $r['response_header'][0] == 'HTTP/1.1 200 OK';
    }
}