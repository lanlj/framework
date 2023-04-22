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
     * cURL配置
     * @var Curl
     */
    private $curl = null;

    /**
     * cURL句柄信息
     * @var array
     */
    private $cURLInfo = null;

    /**
     * UniCurl constructor.
     * @param Curl $curl
     */
    public function __construct(Curl $curl = null)
    {
        $this->curl = $curl;
    }

    /**
     * @return Curl
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * @param Curl $curl
     * @return $this
     */
    public function setCurl(Curl $curl)
    {
        $this->curl = $curl;
        return $this;
    }

    /**
     * @return array
     */
    public function getCURLInfo()
    {
        return $this->cURLInfo;
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
     * @return array
     */
    public function get()
    {
        return $this->exec($this->curl->get());
    }

    /**
     * 执行总操作
     * @param resource $ch
     * @return array
     */
    private function exec($ch)
    {
        $r = curl_exec($ch);
        $err_no = curl_errno($ch);
        $err_msg = curl_error($ch);
        if (!$err_no) {
            $this->cURLInfo = curl_getinfo($ch);
            if ($this->curl->isGetRequestHeader())
                $arr['request_header'] = CurlUtil::parseHeader(curl_getinfo($ch, CURLINFO_HEADER_OUT));
            if ($this->curl->isGetResponseHeader()) {
                $headerSize = $this->cURLInfo['header_size']; //获得响应结果里的：头大小
                $arr['response_header'] = CurlUtil::parseHeader(substr($r, 0, $headerSize)); //根据头大小去获取头内容
                $arr['response_body'] = substr($r, $headerSize);
            } else $arr['response_body'] = $r;
        } else {
            $arr['err_no'] = $err_no;
            $arr['err_msg'] = $err_msg;
        }

        curl_close($ch);
        return $arr;
    }

    /**
     * 执行POST操作
     * @return array
     */
    public function post()
    {
        return $this->exec($this->curl->post());
    }

    /**
     * 执行POST FILE操作
     * @return array
     */
    public function postFile()
    {
        return $this->exec($this->curl->postFile());
    }

    /**
     * 执行SAFE POST FILE操作
     * @return array
     */
    public function postFileSafety()
    {
        return $this->exec($this->curl->postFileSafety());
    }

    /**
     * 执行HEAD操作
     * @return array
     */
    public function head()
    {
        return $this->exec($this->curl->head());
    }
}