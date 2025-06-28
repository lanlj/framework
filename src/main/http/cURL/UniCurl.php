<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/31
 * Time: 22:47
 */

namespace lanlj\fw\http\cURL;

use lanlj\fw\util\CurlUtil;

final class UniCurl
{
    /**
     * cURL对象
     * @var Curl
     */
    private Curl $curl;

    /**
     * UniCurl constructor.
     * @param Curl $curl
     */
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    /**
     * @return Curl
     */
    public function getCurl(): Curl
    {
        return $this->curl;
    }

    /**
     * @param Curl $curl
     * @return $this
     */
    public function setCurl(Curl $curl): self
    {
        $this->curl = $curl;
        return $this;
    }

    /**
     * UniCurl destructor.
     */
    public function __destruct()
    {
        unset($this->curl);
    }

    /**
     * 执行GET操作
     * @return CurlPacket
     */
    public function get(): CurlPacket
    {
        return $this->exec($this->curl->get());
    }

    /**
     * 执行总操作
     * @param resource $ch
     * @return CurlPacket
     */
    private function exec($ch): CurlPacket
    {
        $result = curl_exec($ch);
        $packet = new CurlPacket(curl_errno($ch), curl_error($ch), curl_getinfo($ch));
        if ($packet->getErrNo() == 0) {
            if ($this->curl->isGetRequestHeaders())
                $packet->setRequestHeaders(CurlUtil::parseHeaders(curl_getinfo($ch, CURLINFO_HEADER_OUT)));
            if ($this->curl->isGetResponseHeaders()) {
                $headerSize = $packet->getCURLInfo()['header_size']; //获得句柄信息里的头大小
                $packet->setResponseHeaders(CurlUtil::parseHeaders(substr($result, 0, $headerSize))); //根据头大小去获取头内容
                $packet->setResponseBody(substr($result, $headerSize));
            } else $packet->setResponseBody($result);
        }
        curl_close($ch);
        return $packet;
    }

    /**
     * 执行POST操作
     * @return CurlPacket
     */
    public function post(): CurlPacket
    {
        return $this->exec($this->curl->post());
    }

    /**
     * 执行POST FILE操作
     * @return CurlPacket
     */
    public function postFile(): CurlPacket
    {
        return $this->exec($this->curl->postFile());
    }

    /**
     * 执行SAFE POST FILE操作
     * @return CurlPacket
     */
    public function postFileSafety(): CurlPacket
    {
        return $this->exec($this->curl->postFileSafety());
    }

    /**
     * 执行HEAD操作
     * @return CurlPacket
     */
    public function head(): CurlPacket
    {
        return $this->exec($this->curl->head());
    }
}