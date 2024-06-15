<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/23
 * Time: 15:56
 */

namespace lanlj\fw\http\cURL;

use lanlj\fw\util\CurlUtil;

final class MultiCurl
{
    const GET = "G";
    const POST = "P";
    const POST_FILE = "PF";
    const POST_FILE_SAFETY = "PFS";

    /**
     * 批处理cURL句柄
     * @var resource
     */
    private $mh = null;

    /**
     * 所有cURL标签
     * @var array
     */
    private array $labels = [];

    /**
     * 所有cURL配置
     * @var array
     */
    private array $curls = [];

    /**
     * 所有cURL句柄信息
     * @var array
     */
    private array $cURLInfo = [];

    /**
     * MultiCurl constructor.
     * @param resource $mh
     */
    public function __construct($mh = null)
    {
        $this->setMh($mh);
    }

    /**
     * @param resource $mh
     * @return $this
     */
    public function setMh($mh): self
    {
        $this->closeMh();
        if (!is_resource($mh)) $mh = curl_multi_init();
        $this->mh = $mh;
        return $this;
    }

    /**
     * Close resource
     */
    private function closeMh(): void
    {
        if (is_resource($this->mh)) {
            unset($this->labels);
            unset($this->curls);
            curl_multi_close($this->mh);
        }
    }

    /**
     * @return array
     */
    public function getCURLInfo(): array
    {
        return $this->cURLInfo;
    }

    /**
     * @param string $label
     * @param Curl $curl
     * @param string $method
     * @return $this
     */
    public function setCurl(string $label, Curl $curl, string $method = self::GET): self
    {
        switch ($method) {
            case self::POST:
                $ch = $curl->post();
                break;
            case self::POST_FILE:
                $ch = $curl->postFile();
                break;
            case self::POST_FILE_SAFETY:
                $ch = $curl->postFileSafety();
                break;
            default:
                $ch = $curl->get();
                break;
        }
        $this->labels[(string)$ch] = $label;
        $this->curls[(string)$ch] = $curl;
        curl_multi_add_handle($this->mh, $ch);
        return $this;
    }

    /**
     * @return array
     */
    public function exec(): array
    {
        $responses = array();
        $active = null;
        do {
            while (($mrc = curl_multi_exec($this->mh, $active)) == CURLM_CALL_MULTI_PERFORM) ;

            if ($mrc != CURLM_OK) break;

            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($this->mh)) {

                // get the info and content returned on the request
                $ch = $done['handle'];
                $err_msg = curl_error($ch);
                $label = (string)$ch;
                $arr = null;
                if ($err_msg == '') {
                    $cURLInfo = curl_getinfo($ch);
                    $curl = Curl::mapping($this->curls[$label]);
                    $r = curl_multi_getcontent($ch);
                    if ($curl->isGetRequestHeader())
                        $arr['request_header'] = CurlUtil::parseHeader(curl_getinfo($ch, CURLINFO_HEADER_OUT));
                    if ($curl->isGetResponseHeader()) {
                        $headerSize = $cURLInfo['header_size']; //获得响应结果里的：头大小
                        $arr['response_header'] = CurlUtil::parseHeader(substr($r, 0, $headerSize)); //根据头大小去获取头内容
                        $arr['response_body'] = substr($r, $headerSize);
                    } else $arr['response_body'] = $r;
                    $this->cURLInfo[$this->labels[$label]] = $cURLInfo;
                } else {
                    $arr['err_msg'] = $err_msg;
                }
                $responses[$this->labels[$label]] = $arr;

                // remove the curl handle that just completed
                curl_multi_remove_handle($this->mh, $ch);
                curl_close($ch);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($this->mh);
            }

        } while ($active);

        $this->closeMh();
        return $responses;
    }

    /**
     * MultiCurl destructor.
     */
    public function __destruct()
    {
        $this->closeMh();
    }
}