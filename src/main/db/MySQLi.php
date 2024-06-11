<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/10
 * Time: 21:39
 */

namespace lanlj\fw\db;

use Exception;
use ezsql\Config;
use ezsql\Database\ez_mysqli;
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class MySQLi implements DB, BeanMapping
{
    /**
     * @var string
     */
    protected string $user;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected ?string $host;

    /**
     * @var string
     */
    protected ?string $port;

    /**
     * @var string
     */
    protected ?string $charset;

    /**
     * MySQLi constructor.
     * @param string $user
     * @param string $password
     * @param string $name
     * @param string $host
     * @param string $port
     * @param string $charset
     */
    public function __construct(string $user, string $password, string $name, string $host = null, string $port = null, string $charset = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->name = $name;
        $this->host = $host;
        $this->port = $port;
        $this->charset = $charset;
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
            $values->get('user', ''),
            $values->get('password', ''),
            $values->get('name', ''),
            $values->get('host'),
            $values->get('port'),
            $values->get('charset')
        );
    }

    /**
     * Get MySQLi database object
     * @return ez_mysqli
     * @throws Exception
     */
    public function getDBO(): ez_mysqli
    {
        return new ez_mysqli(new Config('mysqli', [
            $this->user, $this->password, $this->name, $this->host, $this->port, $this->charset
        ]));
    }
}