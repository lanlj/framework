<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13
 * Time: 20:35
 */

namespace lanlj\fw\db;

use ezSQL_sqlite3;
use ezSQLcore;
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class SQLite implements DB, BeanMapping
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $name;

    /**
     * SQLite constructor.
     * @param string $path
     * @param string $name
     * @param bool $eval
     */
    public function __construct($path = null, $name = null, $eval = false)
    {
        if ($eval) $path = eval("return $path");
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * @param object|array $values
     * @return $this
     */
    public static function mapping($values)
    {
        if ($values instanceof self)
            return $values;
        $values = new Arrays($values);
        return new self(
            $values->get('path'),
            $values->get('name'),
            $values->get('eval', false)
        );
    }

    /**
     * @return ezSQLcore
     */
    public function getDBO()
    {
        return new ezSQL_sqlite3($this->path, $this->name);
    }
}