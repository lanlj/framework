<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/6/25
 * Time: 23:28
 */

namespace lanlj\fw\http\cURL;

use lanlj\fw\base\Arrays;
use lanlj\fw\bean\BeanMapping;

final class CurlPacket implements BeanMapping
{
    /**
     * 错误代码
     * @var int
     */
    private int $errNo;

    /**
     * 错误信息
     * @var string
     */
    private string $errMsg;

    /**
     * cURL句柄信息
     * @var array
     */
    private array $cURLInfo;

    /**
     * 请求头
     * @var array|null
     */
    private ?array $requestHeaders = null;

    /**
     * 响应头
     * @var array|null
     */
    private ?array $responseHeaders = null;

    /**
     * 状态码
     * @var string|null
     */
    private ?string $statusCode = null;

    /**
     * 响应体
     * @var mixed
     */
    private $responseBody;

    /**
     * @param int $errNo
     * @param string $errMsg
     * @param array $cURLInfo
     */
    public function __construct(int $errNo, string $errMsg, array $cURLInfo)
    {
        $this->errNo = $errNo;
        $this->errMsg = $errMsg;
        $this->cURLInfo = $cURLInfo;
    }

    /**
     * @param object|array $args
     * @return self
     */
    public static function mapping($args): self
    {
        if ($args instanceof self) return $args;
        $args = new Arrays($args);
        return (new self(
            $args->get('errNo'),
            $args->get('errMsg'),
            $args->get('cURLInfo')
        ))->setRequestHeaders($args->get('requestHeaders'))
            ->setResponseHeaders($args->get('responseHeaders'))
            ->setResponseBody($args->get('responseBody'));
    }

    /**
     * @return int
     */
    public function getErrNo(): int
    {
        return $this->errNo;
    }

    /**
     * @return string
     */
    public function getErrMsg(): string
    {
        return $this->errMsg;
    }

    /**
     * @return array
     */
    public function getCURLInfo(): array
    {
        return $this->cURLInfo;
    }

    /**
     * @return array|null
     */
    public function getRequestHeaders(): ?array
    {
        return $this->requestHeaders;
    }

    /**
     * @param array $requestHeaders
     * @return $this
     */
    public function setRequestHeaders(array $requestHeaders): self
    {
        $this->requestHeaders = $requestHeaders;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getResponseHeaders(): ?array
    {
        return $this->responseHeaders;
    }

    /**
     * @param array $responseHeaders
     * @return $this
     */
    public function setResponseHeaders(array $responseHeaders): self
    {
        $this->responseHeaders = $responseHeaders;
        $this->statusCode = $responseHeaders[0];
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatusCode(): ?string
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param mixed $responseBody
     * @return $this
     */
    public function setResponseBody($responseBody): self
    {
        $this->responseBody = $responseBody;
        return $this;
    }
}