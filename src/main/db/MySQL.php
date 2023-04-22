<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13
 * Time: 20:34
 */

namespace lanlj\fw\db;

use Exception;
use ezSQL_pdo;
use ezSQLcore;
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class MySQL implements DB, BeanMapping
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * MySQL constructor.
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $password
     */
    public function __construct($host = null, $name = null, $user = null, $password = null)
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;
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
            $values->get('host'),
            $values->get('name'),
            $values->get('user'),
            $values->get('password')
        );
    }

    /**
     * @return ezSQLcore
     * @throws Exception
     */
    public function getDBO()
    {
        $dbo = new ezSQL_pdo("mysql:host=$this->host;dbname=$this->name", $this->user, $this->password);
        $dbo->query("set names utf8;");
        return $dbo;
    }
}