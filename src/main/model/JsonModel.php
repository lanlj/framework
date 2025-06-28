<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 14:04
 */

namespace lanlj\fw\model;

use lanlj\fw\core\Arrays;

class JsonModel
{
    /**
     * 资源代码
     * @var string
     */
    private string $resCode;

    /**
     * 资源信息
     * @var string
     */
    private string $resMsg;

    /**
     * 业务响应数据
     * @var mixed|null
     */
    private $busiDataResp = null;

    /**
     * 附加参数
     * @var Arrays
     */
    private Arrays $attributes;

    /**
     * JsonModel constructor.
     * @param string $resCode
     * @param string $resMsg
     */
    public function __construct(string $resCode, string $resMsg)
    {
        $this->resCode = $resCode;
        $this->resMsg = $resMsg;
        $this->attributes = new Arrays();
    }

    /**
     * @return string
     */
    public function getResCode(): string
    {
        return $this->resCode;
    }

    /**
     * @param string $resCode
     * @return $this
     */
    public function setResCode(string $resCode): self
    {
        $this->resCode = $resCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getResMsg(): string
    {
        return $this->resMsg;
    }

    /**
     * @param string $resMsg
     * @return $this
     */
    public function setResMsg(string $resMsg): self
    {
        $this->resMsg = $resMsg;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getBusiDataResp()
    {
        return $this->busiDataResp;
    }

    /**
     * @param mixed|null $busiDataResp
     * @return $this
     */
    public function setBusiDataResp($busiDataResp): self
    {
        $this->busiDataResp = $busiDataResp;
        return $this;
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
     * @return $this
     */
    public function setAttribute(string $name, $value): self
    {
        $this->attributes->add($value, $name);
        return $this;
    }
}