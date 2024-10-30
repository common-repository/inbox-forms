<?php

namespace Inbox\Admin;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

if (! defined('ABSPATH')) {
    exit;
}

class Settings extends PageBase
{
    private static $instance;

    public function __construct()
    {
        parent::__construct();

        global $inboxError;

        add_action('admin_menu', [$this, 'registerSettingsPage']);
    }

    public function registerSettingsPage()
    {
        add_submenu_page(
            INBOX_SETTINGS_FORMS_SLUG,
            __('Settings - INBOX', 'inbox'),
            __('Settings', 'inbox'),
            'manage_options',
            INBOX_SETTINGS_SETTINGS_SLUG,
            [$this, 'adminSettingsPage']
        );
    }

    public function adminSettingsPage()
    {
        if (isset($_POST['save_inbox_settings'])) {
            check_admin_referer('wp_nonce', 'wp_nonce');

            unset($inboxError);

            $key = str_replace('*', '', sanitize_text_field($_POST['inbox_api_key']));

            if (empty($key)) {
                update_option('inbox_api_key', $key);
                update_option('inbox_enabled', false);
            } else {
                if (! validateAlphaNum($key)) {
                    $inboxError = "API Key is not valid.";
                }

                if (substr(get_option('inbox_api_key'), -10) !== $key) {
                    try {
                        $response = $this->apiClient->get(INBOX_API_BASE_URL . '/inbox/auth-check', ['headers' => ['x-api-key' => $key]]);
                    } catch (\Exception $e) {
                        if ($e instanceof RequestException) {
                            $response = $e->getResponse();
                        } else {
                            $response = new Response(500);
                        }
                    }

                    if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                        update_option('inbox_api_key', $key);
                        update_option('inbox_enabled', true);
                    } else {
                        if ($response->getStatusCode() == 401) {
                            $inboxError = "API Key is not valid.";
                        } else {
                            $inboxError = "Error encountered: " . $response->getReasonPhrase();
                        }
                    }
                }
            }
        }

        $apiKey = empty(get_option('inbox_api_key')) ? get_option('inbox_api_key') : str_pad('', 10, '*') . substr(get_option('inbox_api_key'), -10);

        include(INBOX_ROOT . 'templates/admin/settings.php');
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
