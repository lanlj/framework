<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 14:04
 */

namespace lanlj\fw\json;

use lanlj\fw\core\Arrays;
use lanlj\fw\util\BooleanUtil;

final class AjaxJson
{
    /**
     * 是否成功
     * @var bool
     */
    private $success = false;

    /**
     * 提示信息
     * @var string
     */
    private $msg = "操作失败";

    /**
     * 其他信息
     * @var mixed
     */
    private $obj = null;

    /**
     * 其他参数
     * @var Arrays
     */
    private $attributes = null;

    /**
     * AjaxJson constructor.
     */
    public function __construct()
    {
        $this->attributes = new Arrays();
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        $this->success = BooleanUtil::toBool($this->success);
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg($msg)
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
    public function getAttributes()
    {
        return $this->attributes->isEmpty() ? NULL : $this->attributes->getArray();
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes->add($value, $name);
    }
}