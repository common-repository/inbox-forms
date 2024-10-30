<?php

namespace Inbox;

class DatabaseMigration
{
    const FORMS_TABLE_NAME = 'inbox_forms';

    const DB_VER = 1;

    private static $instance;

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function create()
    {
        global $wpdb;

        $collate = '';
        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $formsTableName = $wpdb->prefix . self::FORMS_TABLE_NAME;

        $sqls[] = "CREATE TABLE IF NOT EXISTS $formsTableName (
                  id mediumint(9) NOT NULL AUTO_INCREMENT,
                  form_id varchar(24) NOT NULL,
                  url tinytext NOT NULL,
                  name varchar(50) NOT NULL,
                  type tinyint(1) default '1' NOT NULL,
                  selector tinytext NULL,
                  timeout mediumint(9) NULL,
                  hours smallint(6) NULL,
                  rules text NULL,
                  PRIMARY KEY (id),
                  UNIQUE KEY name (name)
                ) $collate;
				";

        $sqls = apply_filters('ib_create_database_tables', $sqls, $collate);

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($sqls as $sql) {
            dbDelta($sql);
        }
    }

    public function drop($tables)
    {
        global $wpdb;

        $db_prefix = $wpdb->prefix;

        $tables[] = $db_prefix . self::FORMS_TABLE_NAME;

        $tables = apply_filters('ib_drop_database_tables', $tables, $db_prefix);

        return $tables;
    }

    public function canUpdate()
    {
        add_option('ib_db_ver', 0);

        if (get_option('ib_db_ver') >= self::DB_VER) {
            return;
        }

        $this->update();
    }

    public function update()
    {
        set_time_limit(0);

        $currentDbVer = get_option('ib_db_ver');
        $targetDbVer = self::DB_VER;

        while ($currentDbVer < $targetDbVer) {
            $currentDbVer++;

            $updateMethod = "migration_{$currentDbVer}";

            if (method_exists($this, $updateMethod)) {
                call_user_func([$this, $updateMethod]);
            }

            update_option('ib_db_ver', $currentDbVer);
        }
    }

    public function migration_1()
    {
        global $wpdb;

        $table = $wpdb->prefix . self::FORMS_TABLE_NAME;

        $wpdb->query("ALTER TABLE $table CHANGE hours days smallint(6) NULL;");
        $wpdb->query("ALTER TABLE $table ADD is_active tinyint(1) NOT NULL default '1';");
    }
}
