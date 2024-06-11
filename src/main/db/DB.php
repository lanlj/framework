<?php
/**
 * Created by PhpStorm.
 * User: lanlj
 * Mail: jun@lanlj.com
 * Date: 2018/12/15
 * Time: 19:17
 */

namespace lanlj\fw\db;

use ezsql\ezsqlModel;

interface DB
{
    /**
     * Get database object
     * @return ezsqlModel
     */
    public function getDBO(): ezsqlModel;
}