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
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class SQLite implements DB, BeanMapping
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
    }

    /**
     * @param array|object $values
     * @return self
     */
    public static function mapping($values): self
    {
        if ($values instanceof self)
            return $values;
        $values = new Arrays($values);

        $path = $values->get('path', '');
        $eval = $values->get('eval', false);
        if ($eval) $path = eval("return $path;");
        return new self($path, $values->get('name', ''));
    }

    /**
     * Get SQLite3 database object
     * @return ez_sqlite3
     * @throws Exception
     */
    public function getDBO(): ez_sqlite3
    {
        return new ez_sqlite3(new Config('sqlite3', [$this->path, $this->name]));
    }
}