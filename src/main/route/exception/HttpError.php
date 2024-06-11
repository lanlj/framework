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
    private ?string $err_header;

    /**
     * @var string
     */
    private ?string $err_message;

    /**
     * Error constructor.
     * @param string $err_header
     * @param string $err_message
     */
    public function __construct(string $err_header = null, string $err_message = null)
    {
        $this->err_header = $err_header;
        $this->err_message = $err_message;
    }

    /**
     * @param object|array $values
     * @return self
     */
    public static function mapping($values): self
    {
        if ($values instanceof self)
            return $values;
        $values = new Arrays($values);
        return new self(
            $values->get('err_header'),
            $values->get('err_message')
        );
    }

    /**
     * @return string
     */
    public function getErrHeader(): ?string
    {
        return $this->err_header;
    }

    /**
     * @param string $err_header
     * @return self
     */
    public function setErrHeader(string $err_header): self
    {
        $this->err_header = $err_header;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrMessage(): ?string
    {
        return $this->err_message;
    }

    /**
     * @param string $err_message
     * @return self
     */
    public function setErrMessage(string $err_message): self
    {
        $this->err_message = $err_message;
        return $this;
    }
}