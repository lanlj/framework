<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13
 * Time: 20:35
 */

namespace lanlj\fw\db;

use Exception;
use ezsql\Config;
use ezsql\Database\ez_sqlite3;
use lanlj\fw\core\Arrays;

class SQLite extends DB
{
    /**
     * Database file path
     * @var string
     */
    protected string $path;

    /**
     * Database filename
     * @var string
     */
    protected string $name;

    /**
     * SQLite constructor.
     * @param string $path
     * @param string $name
     */
    public function __construct(string $path, string $name)
    {
        $this->path = $path;
        $this->name = $name;
        try {
            $this->dbo = new ez_sqlite3(new Config('sqlite3', [$this->path, $this->name]));
        } catch (Exception $e) {
        }
    }

    /**
     * @param array|object $args
     * @return self
     */
    public static function mapping($args): self
    {
        if ($args instanceof self)
            return $args;
        $args = new Arrays($args);
        $path = $args->get('path', '');
        $eval = $args->get('eval', false);
        if ($eval) $path = eval("return $path;");
        return new self($path, $args->get('name', ''));
    }
}