<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/15
 * Time: 19:17
 */

namespace lanlj\fw\db;

use ezsql\ezsqlModel;
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\proxy\SqlLogProxy;
use microAOP\Proxy;

abstract class DB implements BeanMapping
{
    /**
     * @var ezsqlModel|null
     */
    protected $dbo;

    /**
     * @var string|null
     */
    private ?string $logFile;

    /**
     * @return string|null
     */
    public function getLogFile(): ?string
    {
        return $this->logFile;
    }

    /**
     * @param string|null $logFile
     * @return DB
     */
    public function setLogFile(?string $logFile): DB
    {
        $this->logFile = $logFile;
        return $this;
    }

    /**
     * Get database object
     * @return ezsqlModel|null
     */
    public function getDBO()
    {
        return $this->dbo;
    }

    /**
     * 调用此方法以开启SQL日志
     * 可继承SqlLogProxy类
     * 重写execute静态方法后传入
     * @param SqlLogProxy|null $proxy
     */
    public function initProxyDBO(SqlLogProxy $proxy = null): void
    {
        $class = !is_null($proxy) ? get_class($proxy) : '\lanlj\fw\proxy\SqlLogProxy';
        Proxy::__bind_func__($this->dbo, '/.*/', 'always', array($class, 'execute'));
    }
}