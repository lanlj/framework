<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 11:33
 */

namespace lanlj\fw\http\cURL;

use lanlj\fw\base\Arrays;
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\http\url\Url;
use lanlj\fw\util\ArrayUtil;

final class Curl implements BeanMapping
{
    /**
     * cURL句柄
     * @var resource
     */
    private $ch = null;

    /**
     * 请求网址
     * @var string
     */
    private string $url;

    /**
     * 请求数据
     * @var Arrays
     */
    private Arrays $payload;

    /**
     * 请求头
     * @var Arrays
     */
    private Arrays $headers;

    /**
     * 是否CA验证
     * @var bool
     */
    private bool $verifyPeer = false;

    /**
     * 获取请求头
     * @var bool
     */
    private bool $getRequestHeaders = false;

    /**
     * 获取响应头
     * @var bool
     */
    private bool $getResponseHeaders = false;

    /**
     * 自动生成来源页
     * @var bool
     */
    private bool $autoGenerateReferer = false;

    /**
     * Curl constructor.
     * @param resource $ch
     */
    public function __construct($ch = null)
    {
        $this->payload = new Arrays();
        $this->headers = new Arrays();
        $this->setCurlHandle($ch);
    }

    /**
     * @param resource $ch
     * @return $this
     */
    public function setCurlHandle($ch): self
    {
        $this->closeCurlHandle();
        if (!is_resource($ch)) $ch = curl_init();
        $this->ch = $ch;
        return $this;
    }

    /**
     * Close resource
     */
    private function closeCurlHandle(): void
    {
        if (is_resource($this->ch)) curl_close($this->ch);
    }

    /**
     * @param object|array $args
     * @return $this
     */
    public static function mapping($args): ?self
    {
        return $args instanceof self ? $args : null;
    }

    /**
     * 设置请求数据
     * @param array|object $payload
     * @return $this
     */
    public function setPayload($payload): self
    {
        $this->payload->clear()->addAll(ArrayUtil::toArray($payload));
        return $this;
    }

    /**
     * 设置请求头
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setHeader(string $name, $value): self
    {
        $this->headers->add($value, $name);
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers->addAll($headers);
        return $this;
    }

    /**
     * @param array|object $options
     * @return $this
     */
    public function setOptArray($options): self
    {
        curl_setopt_array($this->ch, ArrayUtil::toArray($options));
        return $this;
    }

    /**
     * @param bool $verifyPeer
     * @return $this
     */
    public function setVerifyPeer(bool $verifyPeer): self
    {
        $this->verifyPeer = $verifyPeer;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGetRequestHeaders(): bool
    {
        return $this->getRequestHeaders;
    }

    /**
     * @param bool $getRequestHeaders
     * @return self
     */
    public function setGetRequestHeaders(bool $getRequestHeaders): self
    {
        $this->getRequestHeaders = $getRequestHeaders;
        return $this->setOpt(CURLINFO_HEADER_OUT, $getRequestHeaders); //获取请求头
    }

    /**
     * 设置cURL选项
     * @param int $option
     * @param mixed $value
     * @return $this
     */
    public function setOpt(int $option, $value): self
    {
        curl_setopt($this->ch, $option, $value);
        return $this;
    }

    /**
     * @return bool
     */
    public function isGetResponseHeaders(): bool
    {
        return $this->getResponseHeaders;
    }

    /**
     * @param bool $getResponseHeaders
     * @return $this
     */
    public function setGetResponseHeaders(bool $getResponseHeaders): self
    {
        $this->getResponseHeaders = $getResponseHeaders;
        return $this->setOpt(CURLOPT_HEADER, $getResponseHeaders); //获取响应头
    }

    /**
     * @param bool $autoGenerateReferer
     * @return $this
     */
    public function setAutoGenerateReferer(bool $autoGenerateReferer): self
    {
        $this->autoGenerateReferer = $autoGenerateReferer;
        return $this;
    }

    /**
     * @return $this
     */
    public function setDefaultTimeout(): self
    {
        return $this->setTimeout(30);
    }

    /**
     * 设置超时秒数
     * @param int $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): self
    {
        return $this->setOpt(CURLOPT_TIMEOUT, $timeout);
    }

    /**
     * @return self
     */
    public function setDefaultUserAgent(): self
    {
        return $this->setUserAgent('Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13');
    }

    /**
     * 设置用户代理
     * @param string $userAgent
     * @return self
     */
    public function setUserAgent(string $userAgent): self
    {
        return $this->setOpt(CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * 设置来源页面
     * @param string $referer
     * @return self
     */
    public function setReferer(string $referer): self
    {
        return $this->setOpt(CURLOPT_REFERER, $referer);
    }

    /**
     * 设置是否跟随重定向以及重定向次数
     * @param bool $followLocation
     * @param int $maxRedirs
     * @return $this
     */
    public function setFollowLocation(bool $followLocation, int $maxRedirs = 0): self
    {
        $this->setOpt(CURLOPT_FOLLOWLOCATION, $followLocation);
        return $maxRedirs < 1 ? $this : $this->setOpt(CURLOPT_MAXREDIRS, $maxRedirs);
    }

    /**
     * Curl destructor.
     */
    public function __destruct()
    {
        $this->closeCurlHandle();
    }

    /**
     * GET
     * @return resource
     */
    public function get()
    {
        $url = (new Url($this->url))
            ->addParamList($this->payload->getArray());
        return $this->setUrl($url->build())
            ->setOpt(CURLOPT_CUSTOMREQUEST, 'GET')
            ->beforeInit()->ch;
    }

    /**
     * 初始化前
     * @return $this
     */
    private function beforeInit(): self
    {
        $this->setOpt(CURLOPT_RETURNTRANSFER, true); //不直接输出返回值
        $urlInfo = new Url($this->url);
        if ($this->autoGenerateReferer) {
            if (!is_null($scheme = $urlInfo->get(Url::SCHEME)) && !is_null($host = $urlInfo->get(Url::HOST))) {
                $referer = "$scheme://$host";
                $this->setOpt(CURLOPT_REFERER, $referer); //来源页面
            }
        }
        $cacert = dirname(__FILE__) . '/../../../resources/cacert.pem'; //CA根证书
        $SSL = 'https' == $urlInfo->get(Url::SCHEME);
        if ($SSL && $this->verifyPeer) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, true); //只信任CA颁布的证书
            $this->setOpt(CURLOPT_CAINFO, $cacert); //CA根证书（用来验证的网站证书是否是CA颁布）
            $this->setOpt(CURLOPT_SSL_VERIFYHOST, 2); //检查证书中是否设置域名，并且是否与提供的主机名匹配
        } elseif ($SSL && !$this->verifyPeer) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
            $this->setOpt(CURLOPT_SSL_VERIFYHOST, 0); //不检查证书
        }
        return $this->setOpt(CURLOPT_HTTPHEADER, $this->headers->callback(function ($arr) {
            $headers = array();
            foreach ($arr as $k => $v) {
                $headers[] = "$k: $v";
            }
            return $headers;
        }));
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this->setOpt(CURLOPT_URL, $url);
    }

    /**
     * POST
     * @return resource
     */
    public function post()
    {
        return $this->setOpt(CURLOPT_POST, true)
            ->setOpt(CURLOPT_POSTFIELDS, $this->payload->toQueryString())
            ->beforeInit()->ch;
    }

    /**
     * POST FILE
     * @return resource
     */
    public function postFile()
    {
        return $this->setOpt(CURLOPT_POST, true)
            ->setOpt(CURLOPT_SAFE_UPLOAD, false)
            ->setOpt(CURLOPT_POSTFIELDS, $this->payload->getArray())
            ->beforeInit()->ch;
    }

    /**
     * SAFE POST FILE
     * @return resource
     */
    public function postFileSafety()
    {
        return $this->setOpt(CURLOPT_POST, true)
            ->setOpt(CURLOPT_SAFE_UPLOAD, true)
            ->setOpt(CURLOPT_POSTFIELDS, $this->payload->getArray())
            ->beforeInit()->ch;
    }

    /**
     * HEAD
     * @return resource
     */
    public function head()
    {
        return $this->setOpt(CURLOPT_NOBODY, true)
            ->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD')
            ->setGetResponseHeaders(true)->beforeInit()->ch;
    }
}