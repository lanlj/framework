<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/1
 * Time: 21:36
 */

namespace lanlj\fw\http\storage;

use lanlj\fw\util\StringUtil;

final class Cookie
{
    /**
     * @var string
     */
    private ?string $name;

    /**
     * @var string
     */
    private string $value;

    /**
     * @var int
     */
    private int $expire = 0;

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var string
     */
    private string $domain = '';

    /**
     * @var bool
     */
    private bool $secure = false;

    /**
     * @var bool
     */
    private bool $httpOnly = false;

    /**
     * Cookie constructor.
     * @param string $name
     * @param mixed $value
     */
    public function __construct(?string $name, $value)
    {
        $this->name = $name;
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = StringUtil::toString($value);
        return $this;
    }

    /**
     * @return int
     */
    public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * @param int $expire
     * @return $this
     */
    public function setExpire(int $expire): self
    {
        $this->expire = $expire;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @param bool $secure
     * @return $this
     */
    public function setSecure(bool $secure): self
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @param bool $httpOnly
     * @return $this
     */
    public function setHttpOnly(bool $httpOnly): self
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }
}