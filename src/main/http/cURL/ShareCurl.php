<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/26
 * Time: 17:04
 */

namespace lanlj\fw\http\cURL;

use lanlj\fw\util\CurlUtil;

final class ShareCurl
{
    const GET = "G";
    const POST = "P";
    const POST_FILE = "PF";
    const POST_FILE_SAFETY = "PFS";

    /**
     * cURL共享句柄
     * @var resource
     */
    private $sh = null;

    /**
     * 所有cURL标签
     * @var array
     */
    private ?array $labels = null;

    /**
     * 所有cURL配置
     * @var array
     */
    private ?array $curls = null;

    /**
     * 所有cURL句柄
     * @var array
     */
    private ?array $chs = null;

    /**
     * 所有cURL句柄信息
     * @var array
     */
    private ?array $cURLInfo = null;

    /**
     * ShareCurl constructor.
     * @param resource $sh
     */
    public function __construct($sh = null)
    {
        $this->setSh($sh);
    }

    /**
     * @param resource $sh
     * @return $this
     */
    public function setSh($sh): self
    {
        $this->closeSh();
        if (!is_resource($sh)) $sh = curl_share_init();
        $this->sh = $sh;
        return $this;
    }

    /**
     * Close resource
     */
    private function closeSh(): void
    {
        if (is_resource($this->sh)) {
            unset($this->labels);
            unset($this->curls);
            unset($this->chs);
            curl_share_close($this->sh);
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
     * @param int $option
     * @param mixed $value
     * @return $this
     */
    public function setOpt(int $option, $value): self
    {
        curl_share_setopt($this->sh, $option, $value);
        return $this;
    }

    /**
     * @param string $label
     * @param Curl $curl
     * @param string $method
     * @return $this
     */
    public function setCurl(?string $label, Curl $curl, ?string $method = self::GET): self
    {
        $curl->setOpt(CURLOPT_SHARE, $this->sh);
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
        $this->chs[] = $ch;
        $this->labels[(string)$ch] = $label;
        $this->curls[(string)$ch] = $curl;
        return $this;
    }

    /**
     * @return array
     */
    public function exec(): array
    {
        $responses = array();
        foreach ($this->chs as $ch) {
            $r = curl_exec($ch);
            $err_no = curl_errno($ch);
            $err_msg = curl_error($ch);
            $label = (string)$ch;
            $arr = null;
            if (!$err_no) {
                $cURLInfo = curl_getinfo($ch);
                $curl = Curl::mapping($this->curls[$label]);
                if ($curl->isGetRequestHeader())
                    $arr['request_header'] = CurlUtil::parseHeader(curl_getinfo($ch, CURLINFO_HEADER_OUT));
                if ($curl->isGetResponseHeader()) {
                    $headerSize = $cURLInfo['header_size']; //获得响应结果里的：头大小
                    $arr['response_header'] = CurlUtil::parseHeader(substr($r, 0, $headerSize)); //根据头大小去获取头内容
                    $arr['response_body'] = substr($r, $headerSize);
                } else $arr['response_body'] = $r;
                $this->cURLInfo[$this->labels[$label]] = $cURLInfo;
            } else {
                $arr['err_no'] = $err_no;
                $arr['err_msg'] = $err_msg;
            }
            $responses[$this->labels[$label]] = $arr;

            curl_close($ch);
        }

        $this->closeSh();
        return $responses;
    }

    /**
     * ShareCurl destructor.
     */
    public function __destruct()
    {
        $this->closeSh();
    }
}