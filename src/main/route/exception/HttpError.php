<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2019/8/30
 * Time: 9:56
 */

namespace lanlj\fw\route\exception;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class HttpError implements BeanMapping
{
    /**
     * @var string
     */
    private string $errHeader;

    /**
     * @var string
     */
    private string $errMessage;

    /**
     * Error constructor.
     * @param string $errHeader
     * @param string $errMessage
     */
    public function __construct(string $errHeader, string $errMessage)
    {
        $this->errHeader = $errHeader;
        $this->errMessage = $errMessage;
    }

    /**
     * @param object|array $args
     * @return self
     */
    public static function mapping($args): self
    {
        if ($args instanceof self) return $args;
        $args = new Arrays($args);
        return new self(
            $args->get('errHeader'),
            $args->get('errMessage')
        );
    }

    /**
     * @return string
     */
    public function getErrHeader(): string
    {
        return $this->errHeader;
    }

    /**
     * @param string $errHeader
     * @return self
     */
    public function setErrHeader(string $errHeader): self
    {
        $this->errHeader = $errHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrMessage(): string
    {
        return $this->errMessage;
    }

    /**
     * @param string
     * @return self
     */
    public function setErrMessage(string $errMessage): self
    {
        $this->errMessage = $errMessage;
        return $this;
    }
}