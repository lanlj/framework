<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/10
 * Time: 21:39
 */

namespace lanlj\fw\db;

use Error;
use Exception;
use ezsql\Config;
use ezsql\Database\ez_mysqli;
use lanlj\fw\base\Arrays;

class MySQLi extends DB
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
     * @var string|null
     */
    protected ?string $host;

    /**
     * @var string|null
     */
    protected ?string $port;

    /**
     * @var string|null
     */
    protected ?string $charset;

    /**
     * MySQLi constructor.
     * @param string $user
     * @param string $password
     * @param string $name
     * @param string|null $host
     * @param string|null $port
     * @param string|null $charset
     */
    public function __construct(
        string $user, string $password, string $name, string $host = null, string $port = null, string $charset = null
    )
    {
        $this->user = $user;
        $this->password = $password;
        $this->name = $name;
        $this->host = $host;
        $this->port = $port;
        $this->charset = $charset;
        try {
            $this->dbo = new ez_mysqli(new Config('mysqli', [
                $this->user, $this->password, $this->name, $this->host, $this->port, $this->charset
            ]));
        } catch (Error | Exception $e) {
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
        return new self(
            $args->get('user', ''),
            $args->get('password', ''),
            $args->get('name', ''),
            $args->get('host'),
            $args->get('port'),
            $args->get('charset')
        );
    }
}