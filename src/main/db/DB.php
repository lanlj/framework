<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/15
 * Time: 19:17
 */

namespace lanlj\fw\db;

use ezSQLcore;

interface DB
{
    /**
     * @return ezSQLcore
     */
    public function getDBO();
}