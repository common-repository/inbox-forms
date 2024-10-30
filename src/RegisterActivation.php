<?php

namespace Inbox;

class RegisterActivation
{
    public static function run($networkwide)
    {
        if (is_multisite() && $networkwide) {

            $site_ids = get_sites(['fields' => 'ids', 'number' => 0]);

            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                self::ibInstall();
                restore_current_blog();
            }
        } else {
            self::ibInstall();
        }
    }

    public static function runDeactivation($networkwide)
    {
        if (is_multisite() && $networkwide) {

            $site_ids = get_sites(['fields' => 'ids', 'number' => 0]);

            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                self::ibDeactivate();
                restore_current_blog();
            }
        } else {
            self::ibDeactivate();
        }
    }

    public static function ibMultisiteInstall($blog_id)
    {
        if (! function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (is_plugin_active_for_network('inbox-form/inbox-form.php')) {
            switch_to_blog($blog_id);
            self::ibInstall();
            restore_current_blog();
        }
    }

    public static function ibInstall()
    {
        if (! current_user_can('activate_plugins') || get_option('ib_plugin_activated') == 'true') {
            return;
        }

        DatabaseMigration::getInstance()->create();
        self::configureDefaultSettings();

        add_option('ib_install_date', current_time('mysql'));
        add_option('ib_plugin_activated', 'true');
    }

    public static function ibDeactivate()
    {
        if (! current_user_can('activate_plugins') || get_option('ib_plugin_activated') != 'true') {
            return;
        }

        delete_option('ib_plugin_activated');
    }

    public static function configureDefaultSettings()
    {
        add_option(INBOX_SETTINGS_DB_OPTION_NAME, []);
    }
}
