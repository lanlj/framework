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
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class PDO implements DB, BeanMapping
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
     * @var array
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
     * @param array $options
     * @param bool $isFile
     */
    public function __construct(string $dsn, string $user, string $password, array $options = null, bool $isFile = false)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->options = $options;
        $this->isFile = $isFile;
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

        $options = $values->get('options');
        if (!is_array($options)) {
            $eval = $values->get('eval', false);
            $eval ? $options = eval("return $options;") : $options = null;
        }
        return new self(
            $values->get('dsn', ''),
            $values->get('user', ''),
            $values->get('password', ''),
            $options,
            $values->get('isFile', false)
        );
    }

    /**
     * Get PDO database object
     * @return ez_pdo
     * @throws Exception
     */
    public function getDBO(): ez_pdo
    {
        return new ez_pdo(new Config('pdo', [$this->dsn, $this->user, $this->password, $this->options, $this->isFile]));
    }
}