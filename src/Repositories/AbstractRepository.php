<?php

namespace Inbox\Repositories;

use Inbox\DatabaseMigration;

abstract class AbstractRepository
{
    /**
     * @return \wpdb|array
     */
    protected static function wpdb()
    {
        return $GLOBALS['wpdb'];
    }

    /**
     * @return string
     */
    public static function forms_table()
    {
        return self::wpdb()->prefix . DatabaseMigration::FORMS_TABLE_NAME;
    }
}
