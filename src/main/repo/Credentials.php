<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/9
 * Time: 16:15
 */

namespace lanlj\fw\repo;

final class Credentials
{
    const OR = "sUyM";
    const AND = "va8e";
    const LIKE = "qeiT";
    const EQUAL = "Pj2Y";

    /**
     * @var array
     */
    private array $modes;

    /**
     * @param string $mode1
     * @param string $mode2
     */
    public function __construct(string $mode1, string $mode2)
    {
        $this->modes = [$mode1, $mode2];
    }

    /**
     * 默认参数
     * @return Credentials
     */
    public static function default(): self
    {
        return new self(self::AND, self::EQUAL);
    }

    /**
     * @return bool
     */
    public function isOR(): bool
    {
        return $this->modes[0] == self::OR;
    }

    /**
     * @return bool
     */
    public function isLIKE(): bool
    {
        return $this->modes[1] == self::LIKE;
    }
}