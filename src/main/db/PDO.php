<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13
 * Time: 20:34
 */

namespace lanlj\fw\db;

use Exception;
use ezsql\Config;
use ezsql\Database\ez_pdo;
use lanlj\fw\core\Arrays;

class PDO extends DB
{
    /**
     * @var string
     */
    protected string $dsn;

    /**
     * @var string
     */
    protected string $user;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var array|null
     */
    protected ?array $options;

    /**
     * @var bool
     */
    protected bool $isFile;

    /**
     * PDO constructor.
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param array|null $options
     * @param bool $isFile
     */
    public function __construct(string $dsn, string $user, string $password, array $options = null, bool $isFile = false)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->options = $options;
        $this->isFile = $isFile;
        try {
            $this->dbo = new ez_pdo(new Config('pdo', [
                $this->dsn, $this->user, $this->password, $this->options, $this->isFile
            ]));
        } catch (Exception $e) {
        }
    }

    /**
     * @param object|array $args
     * @return self
     */
    public static function mapping($args): self
    {
        if ($args instanceof self)
            return $args;
        $args = new Arrays($args);

        $options = $args->get('options');
        if (!is_array($options)) {
            $eval = $args->get('eval', false);
            $eval ? $options = eval("return $options;") : $options = null;
        }
        return new self(
            $args->get('dsn', ''),
            $args->get('user', ''),
            $args->get('password', ''),
            $options,
            $args->get('isFile', false)
        );
    }
}