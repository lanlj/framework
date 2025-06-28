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
    private array $labels = [];

    /**
     * 所有cURL对象
     * @var array
     */
    private array $curls = [];

    /**
     * 所有cURL句柄
     * @var array
     */
    private array $chs = [];

    /**
     * ShareCurl constructor.
     * @param resource $sh
     */
    public function __construct($sh = null)
    {
        $this->setShareHandle($sh);
    }

    /**
     * @param resource $sh
     * @return $this
     */
    public function setShareHandle($sh): self
    {
        $this->closeShareHandle();
        if (!is_resource($sh)) $sh = curl_share_init();
        $this->sh = $sh;
        return $this;
    }

    /**
     * Close resource
     */
    private function closeShareHandle(): void
    {
        if (is_resource($this->sh)) {
            unset($this->labels);
            unset($this->curls);
            unset($this->chs);
            curl_share_close($this->sh);
        }
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
    public function setCurl(string $label, Curl $curl, string $method = self::GET): self
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
        $packets = array();
        foreach ($this->chs as $ch) {
            $result = curl_exec($ch);
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
            curl_close($ch);
        }
        $this->closeShareHandle();
        return $packets;
    }

    /**
     * ShareCurl destructor.
     */
    public function __destruct()
    {
        $this->closeShareHandle();
    }
}