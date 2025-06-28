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
     * 所有cURL对象
     * @var array
     */
    private array $curls = [];

    /**
     * MultiCurl constructor.
     * @param resource $mh
     */
    public function __construct($mh = null)
    {
        $this->setMultiHandle($mh);
    }

    /**
     * @param resource $mh
     * @return $this
     */
    public function setMultiHandle($mh): self
    {
        $this->closeMultiHandle();
        if (!is_resource($mh)) $mh = curl_multi_init();
        $this->mh = $mh;
        return $this;
    }

    /**
     * Close resource
     */
    private function closeMultiHandle(): void
    {
        if (is_resource($this->mh)) {
            unset($this->labels);
            unset($this->curls);
            curl_multi_close($this->mh);
        }
    }

    /**
     * @param int $option
     * @param mixed $value
     * @return $this
     */
    public function setOpt(int $option, $value): self
    {
        curl_multi_setopt($this->mh, $option, $value);
        return $this;
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
        $packets = array();
        $active = null;
        do {
            while (($mrc = curl_multi_exec($this->mh, $active)) == CURLM_CALL_MULTI_PERFORM) ;

            if ($mrc != CURLM_OK) break;

            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($this->mh)) {

                // get the info and content returned on the request
                $ch = $done['handle'];

                $result = curl_multi_getcontent($ch);
                $label = (string)$ch;
                $packet = new CurlPacket(curl_errno($ch), curl_error($ch), curl_getinfo($ch));
                if ($packet->getErrNo() == 0) {
                    $curl = Curl::mapping($this->curls[$label]);
                    if ($curl->isGetRequestHeaders())
                        $packet->setRequestHeaders(CurlUtil::parseHeaders(curl_getinfo($ch, CURLINFO_HEADER_OUT)));
                    if ($curl->isGetResponseHeaders()) {
                        $headerSize = $packet->getCURLInfo()['header_size']; //获得句柄信息里的头大小
                        $packet->setResponseHeaders(CurlUtil::parseHeaders(substr($result, 0, $headerSize))); //根据头大小去获取头内容
                        $packet->setResponseBody(substr($result, $headerSize));
                    } else $packet->setResponseBody($result);
                }
                $packets[$this->labels[$label]] = $packet;

                // remove the curl handle that just completed
                curl_multi_remove_handle($this->mh, $ch);
                curl_close($ch);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($this->mh);
            }

        } while ($active);

        $this->closeMultiHandle();
        return $packets;
    }

    /**
     * MultiCurl destructor.
     */
    public function __destruct()
    {
        $this->closeMultiHandle();
    }
}