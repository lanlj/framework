<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 14:04
 */

namespace lanlj\fw\model;

use lanlj\fw\core\Arrays;

final class AjaxJson
{
    /**
     * 错误代码
     * @var int|null
     */
    private ?int $code = null;

    /**
     * 是否成功
     * @var bool|null
     */
    private ?bool $success = null;

    /**
     * 提示信息
     * @var string|null
     */
    private ?string $msg = null;

    /**
     * 其他信息
     * @var mixed
     */
    private $obj = null;

    /**
     * 其他参数
     * @var Arrays
     */
    private Arrays $attributes;

    /**
     * AjaxJson constructor.
     */
    public function __construct()
    {
        $this->attributes = new Arrays();
    }

    /**
     * @return int
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return bool
     */
    public function isSuccess(): ?bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMsg(): ?string
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg(string $msg)
    {
        $this->msg = $msg;
    }

    /**
     * @return mixed
     */
    public function getObj()
    {
        return $this->obj;
    }

    /**
     * @param mixed $obj
     */
    public function setObj($obj)
    {
        $this->obj = $obj;
    }

    /**
     * @return array
     */
    public function getAttributes(): ?array
    {
        return $this->attributes->isEmpty() ? NULL : $this->attributes->getArray();
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute(string $name, $value)
    {
        $this->attributes->add($value, $name);
    }
}