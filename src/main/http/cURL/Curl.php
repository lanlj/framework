<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 11:33
 */

namespace lanlj\fw\http\cURL;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;
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
    private ?string $url = null;

    /**
     * 请求参数
     * @var Arrays
     */
    private Arrays $data;

    /**
     * 请求头
     * @var Arrays
     */
    private Arrays $headers;

    /**
     * 是否CA认证
     * @var bool
     */
    private bool $CA = false;

    /**
     * 获取请求头
     * @var bool
     */
    private bool $getRequestHeader = false;

    /**
     * 获取响应头
     * @var bool
     */
    private bool $getResponseHeader = false;

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
        $this->data = new Arrays();
        $this->headers = new Arrays();
        $this->setCh($ch);
    }

    /**
     * @param resource $ch
     * @return $this
     */
    public function setCh($ch): self
    {
        $this->closeCh();
        if (!is_resource($ch)) $ch = curl_init();
        $this->ch = $ch;
        return $this;
    }

    /**
     * Close resource
     */
    private function closeCh(): void
    {
        if (is_resource($this->ch)) curl_close($this->ch);
    }

    /**
     * @param object|array $values
     * @return $this
     */
    public static function mapping($values): ?self
    {
        if ($values instanceof self)
            return $values;
        return null;
    }

    /**
     * 设置请求参数
     * @param array|object $data
     * @return $this
     */
    public function setData($data): self
    {
        $this->data->clear()->addAll(ArrayUtil::toArray($data));
        return $this;
    }

    /**
     * 设置请求头
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setHeader(?string $key, $value): self
    {
        $this->headers->add($value, $key);
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
     * @param bool $CA
     * @return $this
     */
    public function setCA(bool $CA): self
    {
        $this->CA = $CA;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGetRequestHeader(): bool
    {
        return $this->getRequestHeader;
    }

    /**
     * @param bool $getRequestHeader
     * @return self
     */
    public function setGetRequestHeader(bool $getRequestHeader): self
    {
        $this->getRequestHeader = $getRequestHeader;
        return $this->setOpt(CURLINFO_HEADER_OUT, $getRequestHeader); //请求头部
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
    public function isGetResponseHeader(): bool
    {
        return $this->getResponseHeader;
    }

    /**
     * @param bool $getResponseHeader
     * @return $this
     */
    public function setGetResponseHeader(bool $getResponseHeader): self
    {
        $this->getResponseHeader = $getResponseHeader;
        return $this->setOpt(CURLOPT_HEADER, $getResponseHeader); //响应头部
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
    public function setUserAgent(?string $userAgent): self
    {
        return $this->setOpt(CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * 设置来源页面
     * @param string $referer
     * @return self
     */
    public function setReferer(?string $referer): self
    {
        return $this->setOpt(CURLOPT_REFERER, $referer);
    }

    /**
     * Curl destructor.
     */
    public function __destruct()
    {
        $this->closeCh();
    }

    /**
     * GET
     * @return resource
     */
    public function get()
    {
        $url = (new Url($this->url))
            ->addParamList($this->data->getArray());
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
        $this->setOpt(CURLOPT_RETURNTRANSFER, 1); //不直接输出返回值
        $urlInfo = new Url($this->url);
        if ($this->autoGenerateReferer) {
            if (!is_null($scheme = $urlInfo->get(Url::SCHEME)) && !is_null($host = $urlInfo->get(Url::HOST))) {
                $referer = "$scheme://$host";
                $this->setOpt(CURLOPT_REFERER, $referer); //来源页面
            }
        }
        $cacert = dirname(__FILE__) . '/../../../resources/cacert.pem'; //CA根证书
        $SSL = 'https' == $urlInfo->get(Url::SCHEME);
        if ($SSL && $this->CA) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, true); //只信任CA颁布的证书
            $this->setOpt(CURLOPT_CAINFO, $cacert); //CA根证书（用来验证的网站证书是否是CA颁布）
            $this->setOpt(CURLOPT_SSL_VERIFYHOST, 2); //检查证书中是否设置域名，并且是否与提供的主机名匹配
        } elseif ($SSL && !$this->CA) {
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
    public function setUrl(?string $url): self
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
            ->setOpt(CURLOPT_POSTFIELDS, $this->data->toQueryString())
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
            ->setOpt(CURLOPT_POSTFIELDS, $this->data->getArray())
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
            ->setOpt(CURLOPT_POSTFIELDS, $this->data->getArray())
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
            ->setGetResponseHeader(true)->beforeInit()->ch;
    }
}