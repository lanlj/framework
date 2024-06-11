<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/9
 * Time: 16:15
 */

namespace lanlj\fw\repo;

class Credentials
{
    const EQ = "m1.Pj2Y";
    const NEQ = "m1.kzEs";
    const LT = "m1.n5ig";
    const LTE = "m1.YqFZ";
    const GT = "m1.ut1U";
    const GTE = "m1.G32h";
    const LIKE = "m1.qeiT";
    const NOT_LIKE = "m1.qWfF";

    const _OR = "m2.sUyM";
    const _AND = "m2.va8e";

    protected const LABELS = [
        self::EQ => "= '%s'", self::NEQ => "!= '%s'", self::LT => "< %u", self::LTE => "<= %u",
        self::GT => "> %u", self::GTE => ">= %u", self::LIKE => "LIKE '%%%s%%'", self::NOT_LIKE => "NOT LIKE '%%%s%%'",
        self::_OR => 'OR', self::_AND => 'AND'
    ];

    /**
     * @var string
     */
    private string $col;

    /**
     * @var string
     */
    private string $m1;

    /**
     * @var string
     */
    private string $m2;

    /**
     * @param string $col
     * @param string $m1
     * @param string $m2
     */
    public function __construct(string $col, string $m1 = self::EQ, string $m2 = self::_AND)
    {
        $this->col = $col;
        $this->m1 = self::LABELS[$m1];
        $this->m2 = self::LABELS[$m2];
    }

    /**
     * @return string
     */
    public function getCol(): string
    {
        return $this->col;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return " $this->m2 $this->col $this->m1";
    }
}