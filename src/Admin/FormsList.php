<?php

namespace Inbox\Admin;

use Inbox\DatabaseMigration;
use Inbox\Repositories\FormRepository;

if (! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class FormsList extends \WP_List_Table
{
    private $table;

    /** @var \wpdb */
    private $wpdb;

    /**
     * Class constructor
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
        $this->table = $this->wpdb->prefix . DatabaseMigration::FORMS_TABLE_NAME;
        parent::__construct([
                'singular' => __('form', 'inbox'),
                'plural' => __('forms', 'inbox'),
                'ajax' => false,
                'screen' => 'forms',
            ]
        );
    }

    /**
     * Retrieve campaigns data from the database
     *
     * @param int $per_page
     * @param int $current_page
     *
     * @return mixed
     */
    public function getForms($per_page, $current_page = 1)
    {
        $per_page = absint($per_page);
        $current_page = absint($current_page);

        $offset = ($current_page - 1) * $per_page;
        $sql = "SELECT * FROM {$this->table}";
        $args = [];

        $sql .= "  ORDER BY id DESC";

        $sql .= " LIMIT %d";

        $args[] = $per_page;

        if ($current_page > 1) {
            $sql .= "  OFFSET %d";
            $args[] = $offset;
        }

        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $args),
            'ARRAY_A'
        );
    }

    /**
     * @param int $id
     */
    public function deleteForm($id)
    {
        FormRepository::deleteForm($id);
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getFormEditUrl($id)
    {
        return add_query_arg(
            [
                'view' => 'edit',
                'id' => absint($id),
            ],
            INBOX_SETTINGS_FORMS_PAGE
        );
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getFormDeleteUrl($id)
    {
        $delete_nonce = wp_create_nonce('inbox_delete_form');

        return add_query_arg(
            [
                'action' => 'delete',
                'id' => absint($id),
                '_wpnonce' => $delete_nonce,
            ],
            INBOX_SETTINGS_FORMS_PAGE
        );
    }

    public function no_items()
    {
        printf(
            __('No forms found. %sConsider creating one%s', 'inbox'),
            '<a href="' . add_query_arg('view', 'add-new', INBOX_SETTINGS_FORMS_PAGE) . '">',
            '</a>'
        );
    }

    public function column_type($item)
    {
        return FormRepository::getTypeName($item['type']);
    }

    public function column_name($item)
    {
        $formId = absint($item['id']);

        $editUrl = self::getFormEditUrl($formId);
        $deleteUrl = self::getFormDeleteUrl($formId);

        $name = "<strong><a href=\"$editUrl\">" . $item['name'] . '</a></strong>';

        $actions = [
            'edit' => sprintf('<a href="%s">%s</a>', $editUrl, __('Edit', 'inbox')),
            'delete' => sprintf('<a class="ib-delete-prompt" href="%s">%s</a>', $deleteUrl, __('Delete', 'inbox')),
        ];

        return $name . $this->row_actions($actions);
    }

    public function column_action($item)
    {
        $id = absint($item['id']);

        $delete_url = self::getFormDeleteUrl($id);
        $editUrl = self::getFormEditUrl($id);

        $action = sprintf(
            '<a class="ib-tooltipster button action inbox-btn-blue" href="%s" title="%s">%s</a> &nbsp;',
            esc_url_raw($editUrl),
            __('Edit', 'inbox'),
            '<span class="dashicons dashicons-edit ib-action-icon"></span>'
        );
        $action .= sprintf(
            '<a class="ib-tooltipster button action inbox-btn-red ib-delete-prompt" href="%s" title="%s">%s</a> &nbsp;',
            $delete_url,
            __('Delete', 'inbox'),
            '<span class="dashicons dashicons-trash ib-action-icon"></span>'
        );

        return $action;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = [
            'name' => __('Name', 'inbox'),
            'type' => __('Form Type', 'inbox'),
            'action' => __('Actions', 'inbox'),
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = [
            'name' => ['name', true],
        ];

        return $sortable_columns;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {
        $this->performActions();

        $this->_column_headers = $this->get_column_info();
        $per_page = $this->get_items_per_page('form_per_page', 15);
        $current_page = $this->get_pagenum();
        $total_items = FormRepository::formCount();
        $this->set_pagination_args([
                'total_items' => $total_items,
                'per_page' => $per_page,
            ]
        );

        $this->items = $this->getForms($per_page, $current_page);
    }

    public function performActions()
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $redirectUrl = INBOX_SETTINGS_FORMS_PAGE;

        $id = @absint($_GET['id']);

        if ('delete' === $this->current_action()) {
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if (! wp_verify_nonce($nonce, 'inbox_delete_form')) {
                wp_nonce_ays('inbox_delete_form');
            } else {
                self::deleteForm($id);
                wp_safe_redirect(esc_url_raw($redirectUrl));
                exit;
            }
        }
    }

    public static function getInstance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self($GLOBALS['wpdb']);
        }

        return $instance;
    }
}
