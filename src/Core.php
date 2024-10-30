<?php

namespace Inbox;

use Inbox\Admin\Forms;
use Inbox\Admin\FormShortcode;
use Inbox\Admin\FormWidget;
use Inbox\Admin\Settings;

if (! defined('ABSPATH')) {
    exit;
}

define('INBOX_API_BASE_URL', 'https://useapi.useinbox.com');

define('INBOX_ROOT', wp_normalize_path(plugin_dir_path(INBOX_SYSTEM_FILE_PATH)));
define('INBOX_URL', plugin_dir_url(INBOX_SYSTEM_FILE_PATH));
define('INBOX_ASSETS_DIR', INBOX_URL . 'assets/');

define('INBOX_SETTINGS_DB_OPTION_NAME', 'inbox_settings');

define('INBOX_SRC', wp_normalize_path(dirname(__FILE__) . '/'));
define('INBOX_SETTINGS_PAGE_FOLDER', wp_normalize_path(dirname(__FILE__) . '/Admin/Pages/'));

define('INBOX_SETTINGS_FORMS_SLUG', 'inbox-forms');
define('INBOX_SETTINGS_SETTINGS_SLUG', 'inbox-settings');

define('INBOX_SETTINGS_SETTINGS_PAGE', admin_url('admin.php?page=' . INBOX_SETTINGS_SETTINGS_SLUG));
define('INBOX_SETTINGS_FORMS_PAGE', admin_url('admin.php?page=' . INBOX_SETTINGS_FORMS_SLUG));

define('INBOX_FORMS_CACHE_KEY', 'inbox_forms');

class Core
{
    private static $instance;

    public function __construct()
    {
        register_activation_hook(INBOX_SYSTEM_FILE_PATH, [RegisterActivation::class, 'run']);
        register_deactivation_hook(INBOX_SYSTEM_FILE_PATH, [RegisterActivation::class, 'runDeactivation']);

        if (version_compare(get_bloginfo('version'), '5.1', '<')) {
            add_action('wpmu_new_blog', [RegisterActivation::class, 'ibMultisiteInstall']);
        } else {
            add_action('wp_initialize_site', function (\WP_Site $new_site) {
                RegisterActivation::ibMultisiteInstall($new_site->blog_id);
            });
        }

        add_action('activate_blog', [RegisterActivation::class, 'ibMultisiteInstall']);

        add_filter('wpmu_drop_tables', [DatabaseMigration::class, 'drop']);

        RegisterScripts::getInstance();

        $this->adminHooks();
        $this->commonHooks();

        add_action('plugins_loaded', [$this, 'dbUpdates']);
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function init()
    {
        self::getInstance();

        do_action('inbox_forms_loaded');
    }

    public function adminHooks()
    {
        if (! is_admin()) {
            return;
        }

        $this->initMenu();
        Forms::getInstance();
        Settings::getInstance();

        do_action('ib_admin_hooks');
    }

    public function commonHooks()
    {
        FormWidget::getInstance();
        FormShortcode::getInstance();

        do_action('ib_common_hooks');
    }

    public function initMenu()
    {
        add_action('admin_menu', [$this, 'registerCoreMenu']);
    }

    public function registerCoreMenu()
    {
        add_menu_page(
            'INBOX',
            'INBOX',
            'manage_options',
            INBOX_SETTINGS_FORMS_SLUG,
            null,
            INBOX_ASSETS_DIR . 'images/plugin-icon.png'
        );

        add_filter('admin_body_class', [$this, 'addAdminBodyClass']);
    }

    public function addAdminBodyClass($classes)
    {
        $current_screen = get_current_screen();

        if (empty($current_screen)) {
            return null;
        }

        if (strpos($current_screen->id, 'inbox') !== false) {
            $classes .= ' inbox-admin ';
        }

        return $classes;
    }

    public function dbUpdates()
    {
        if (! is_admin()) {
            return;
        }

        DatabaseMigration::getInstance()->canUpdate();
    }
}
