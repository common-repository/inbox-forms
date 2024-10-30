<?php

namespace Inbox;

use Inbox\Repositories\FormRepository;

class RegisterScripts
{
    private static $instance;

    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminCss']);
        add_action('admin_enqueue_scripts', [$this, 'adminJs']);
        add_action('admin_enqueue_scripts', [$this, 'fancybox_assets']);
        add_action('wp_enqueue_scripts', [$this, 'publicCss']);
        add_action('wp_enqueue_scripts', [$this, 'publicJs']);
        add_action('wp_enqueue_scripts', [$this, 'fancybox_assets']);

        add_action('enqueue_block_editor_assets', [$this, 'gutenbergJs']);
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function fancybox_assets()
    {
        wp_enqueue_script('inbox-fancybox', INBOX_ASSETS_DIR . 'plugins/fancybox/jquery.fancybox.min.js', ['jquery'], false, true);
        wp_enqueue_style('inbox-fancybox', INBOX_ASSETS_DIR . 'plugins/fancybox/jquery.fancybox.min.css', false, true);
        wp_enqueue_style('inbox-activate-fancybox', INBOX_ASSETS_DIR . 'css/admin/fancybox.css', false, true);
    }

    public function adminJs()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('underscore');
        $this->inboxOnlyJs();
        $this->globalJsVariables();
    }

    public function inboxOnlyJs()
    {
        $screen = get_current_screen();

        $base_text = $screen->base;

        if (strpos($base_text, 'inbox') !== false || is_customize_preview()) {
            wp_enqueue_script('inbox-admin-tooltipster', INBOX_ASSETS_DIR . 'plugins/tooltipster/bundle.min.js', ['jquery'], INBOX_VERSION_NUMBER, true);
            wp_enqueue_script('inbox-admin-tooltipster-init', INBOX_ASSETS_DIR . 'plugins/tooltipster/init.js', [
                'jquery', 'inbox-admin-tooltipster',
            ], INBOX_VERSION_NUMBER, true);

            wp_enqueue_script('inbox-admin-repeater', INBOX_ASSETS_DIR . 'plugins/repeater/jquery.repeater.min.js', ['jquery'], INBOX_VERSION_NUMBER, true);

            wp_enqueue_script('inbox-admin', INBOX_ASSETS_DIR . 'dist/js/inbox-admin.js', ['jquery'], INBOX_VERSION_NUMBER, true);

            do_action('ib_admin_js_enqueue');
        }
    }

    public function gutenbergJs()
    {
        if (! function_exists('register_block_type')) {
            return;
        }

        $templates = [
            0 => [
                'template' => sprintf(
                    __('%s You currently have no form created. Please create one first. %s ', 'inbox'),
                    '<div style="background-color: #fff8e1;border: 1px solid #FFE082;padding: 10px;">',
                    '<div>'),
                'value' => sprintf(
                    __('%s You currently have no form created. Please create one first. %s ', 'inbox'),
                    '<div style="background-color: #fff8e1;border: 1px solid #FFE082;padding: 10px;">',
                    '<div>'),
            ],
        ];
        $formOptions = [];

        $forms = FormRepository::getForms();

        foreach ($forms as $form) {
            $id = $form['id'];

            $formOptions[] = [
                'label' => $form['name'],
                'value' => $id,
            ];

            $templates[$id] = [
                'template' => do_shortcode("[inbox_form form_id=$id]"),
                'value' => "[inbox_form form_id=$id]",
            ];
        }

        wp_register_script(
            'inbox-gutenberg',
            INBOX_ASSETS_DIR . 'dist/js/inbox-block.js',
            [
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-plugins',
                'wp-edit-post',
                'wp-data',
                'wp-compose',
            ],
            INBOX_VERSION_NUMBER,
            true
        );

        $defaultForm = null;

        if (count($templates) > 1) {
            next($templates);
            $defaultForm = key($templates);
        }

        $localizations = [
            'formOptions' => $formOptions,
            'defaultForm' => $defaultForm,
            'icon' => 'feedback',
            'templates' => $templates,
        ];

        wp_localize_script('inbox-gutenberg', 'InboxBlocks', $localizations);

        register_block_type('inbox/form-block', [
            'editor_script' => 'inbox-gutenberg',
        ]);
    }

    public function publicJs()
    {
        $this->modalScripts();

        $this->mobileDetectJs();
    }

    public function mobileDetectJs()
    {
        wp_register_script('ib-mobile-detect', INBOX_ASSETS_DIR . 'js/mobile-detect.min.js', ['jquery'], INBOX_VERSION_NUMBER);
    }

    public function modalScripts()
    {
        wp_enqueue_script('jquery');

        if (is_customize_preview()) {
            wp_enqueue_script('inbox-vendor', INBOX_ASSETS_DIR . 'dist/js/inbox-vendor.js', [
                'jquery',
            ], INBOX_VERSION_NUMBER);
            wp_enqueue_script('inbox', INBOX_ASSETS_DIR . 'dist/js/inbox.js', [
                'jquery', 'inbox-fancybox',
            ], INBOX_VERSION_NUMBER);
        } else {
            wp_enqueue_script('inbox-vendor', INBOX_ASSETS_DIR . 'dist/js/inbox-vendor.js', [
                'jquery',
            ], INBOX_VERSION_NUMBER, true);
            wp_enqueue_script('inbox', INBOX_ASSETS_DIR . 'dist/js/inbox.js', [
                'jquery', 'inbox-fancybox',
            ], INBOX_VERSION_NUMBER, true);
        }

        $this->globalJsVariables('inbox');
    }

    public function google_fonts_script()
    {
        wp_enqueue_script('ib-google-webfont', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', false, INBOX_VERSION_NUMBER, true);
    }

    public function globalJsVariables($handle = 'jquery')
    {
        $localize_strings = [
            'admin_url' => admin_url(),
            'public_js' => INBOX_ASSETS_DIR . 'dist/js',
            'nonce' => wp_create_nonce('inbox-admin-nonce'),
            'chosen_search_placeholder' => __('Type to search', 'inbox'),
            'js_confirm_text' => __('Are you sure?', 'inbox'),
            'js_clear_stat_text' => __('Are you sure want to do this? Clicking OK will delete all your records.', 'inbox'),
            'empty_field_message' => __('This field cannot be left blank.', 'inbox'),
            'wrong_captcha_field_message' => __('Security code is incorrect, please try again.', 'inbox'),
            'wrong_email_field_message' => __('E-mail address is incorrect, please try again.', 'inbox'),
            'text_choose' => __('Choose...', 'inbox'),
            'text_select_all' => __('Select all...', 'inbox'),
            'text_type_to_filter' => __('Type to filter...', 'inbox'),
            'text_selected' => __('Selected', 'inbox'),
            'text_all_selected' => __('All Selected', 'inbox'),
        ];

        if (! is_admin()) {
            unset($localize_strings['admin_url']);
            unset($localize_strings['nonce']);
            unset($localize_strings['chosen_search_placeholder']);
            unset($localize_strings['js_confirm_text']);
            unset($localize_strings['js_clear_stat_text']);
        }

        if (! function_exists('get_current_screen')) {
            $forms = FormRepository::getAutoForms();

            $requestUri = wp_unslash(trim($_SERVER["REQUEST_URI"], '/'));

            foreach ($forms as $form) {
                $rules = json_decode($form['rules'] ?? [], true);

                foreach ($rules as $rule) {
                    $path = wp_unslash(trim($rule['path'], '/'));

                    $matched = false;

                    switch ($rule['type']) {
                        case FormRepository::RULE_TYPE_CONTAIN:
                            $matched = strpos($requestUri, $path) !== false;
                            break;
                        case FormRepository::RULE_TYPE_EQUAL:
                            $matched = $requestUri == $path;
                            break;
                        case FormRepository::RULE_TYPE_NOT_CONTAIN:
                            $matched = strpos($requestUri, $path) === false;
                            break;
                        case FormRepository::RULE_TYPE_NOT_EQUAL:
                            $matched = $requestUri != $path;
                            break;
                    }

                    if ($matched) {
                        $localize_strings['inbox_auto_popup'] = array_intersect_key($form, array_flip([
                            'id', 'url', 'timeout', 'days',
                        ]));
                    }
                }
            }
        }

        wp_localize_script(
            $handle, 'inbox_globals',
            apply_filters('ib_inbox_js_globals', $localize_strings)
        );
    }

    public function adminCss()
    {
        $screen = get_current_screen();

        $base_text = $screen->base;

        if (strpos($base_text, 'inbox') !== false || is_customize_preview()) {
            wp_enqueue_style('inbox-admin-tooltipster', INBOX_ASSETS_DIR . 'plugins/tooltipster/bundle.min.css', [], INBOX_VERSION_NUMBER);
            wp_enqueue_style('inbox-admin-tooltipster-borderless', INBOX_ASSETS_DIR . 'plugins/tooltipster/borderless.min.css', [], INBOX_VERSION_NUMBER);
            wp_enqueue_style('inbox-admin-tooltipster-light', INBOX_ASSETS_DIR . 'plugins/tooltipster/light.min.css', [], INBOX_VERSION_NUMBER);
            wp_enqueue_style('inbox-admin', INBOX_ASSETS_DIR . 'css/admin/admin.css', [], INBOX_VERSION_NUMBER);
        }
    }

    public function publicCss()
    {
        wp_enqueue_style('inbox', INBOX_ASSETS_DIR . 'dist/css/inbox.css', [], INBOX_VERSION_NUMBER);
    }
}
