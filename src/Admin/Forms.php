<?php

namespace Inbox\Admin;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Inbox\Repositories\FormRepository;

if (! defined('ABSPATH')) {
    exit;
}

class Forms extends PageBase
{
    private static $instance;

    public function __construct()
    {
        parent::__construct();

        global $inboxError;

        add_action('admin_menu', [$this, 'registerFormsPage']);
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function registerFormsPage()
    {
        add_submenu_page(
            INBOX_SETTINGS_FORMS_SLUG,
            __('Forms - INBOX', 'inbox'),
            __('Forms', 'inbox'),
            'manage_options',
            INBOX_SETTINGS_FORMS_SLUG,
            [$this, 'adminFormsPage']
        );
    }

    /**
     * @return void
     */
    public function adminFormsPage()
    {
        if (! get_option('inbox_enabled')) {
            wp_safe_redirect(INBOX_SETTINGS_SETTINGS_PAGE);
            exit;
        }

        if (! empty($_GET['view']) && $_GET['view'] == 'add-new') {
            $this->adminFormsAddNewPage();

            return;
        }

        if (! empty($_GET['view']) && $_GET['view'] == 'edit' && ! empty($_GET['id'])) {
            $this->adminFormsEditPage(absint($_GET['id']));

            return;
        }

        add_action('wp_ib_main_content_area', [$this, 'formsListTable'], 10, 0);

        include(INBOX_ROOT . 'templates/admin/forms.php');
    }

    public function formsListTable()
    {
        $formList = FormsList::getInstance();
        $formList->prepare_items();
        $formList->display();
    }

    /**
     * @return void
     */
    private function adminFormsAddNewPage()
    {
        $forms = $this->getInboxForms();

        if (isset($_POST['save_inbox_form'])) {
            check_admin_referer('wp_nonce', 'wp_nonce');

            unset($inboxError);

            $formName = sanitize_text_field($_POST['inbox_form_name'] ?? null);
            $formId = sanitize_text_field($_POST['inbox_form'] ?? null);
            $formType = isset($_POST['inbox_form_type']) ? absint($_POST['inbox_form_type']) : null;
            $formPopupTimeout = isset($_POST['inbox_form_popup_timeout']) ? absint($_POST['inbox_form_popup_timeout']) : null;
            $formPopupSelector = sanitize_text_field($_POST['inbox_form_popup_selector'] ?? null);
            $formDays = isset($_POST['inbox_form_popup_days']) ? absint($_POST['inbox_form_popup_days']) : null;
            $formIsActive = isset($_POST['inbox_form_is_active']) && $_POST['inbox_form_is_active'];
            $formRules = [];

            if (! validateRequired($formName)) {
                $inboxError = __('Form name field is required.', 'inbox');
            } elseif (FormRepository::formExistsByName($formName)) {
                $inboxError = __('Form with similar name exist already.', 'inbox');
            } elseif (! validateRequired($formType) || ! in_array($formType, [
                    FormRepository::TYPE_POPUP, FormRepository::TYPE_INLINE,
                ])) {
                $inboxError = __('Form type is required.', 'inbox');
            } elseif (! validateRequired($formId)) {
                $inboxError = __('Form is required.', 'inbox');
            } elseif (! $inboxForm = (($founds = array_filter($forms, function ($item) use ($formId) {
                return $item['id'] == $formId;
            })) ? array_values($founds)[0] : null)) {
                $inboxError = __('Inbox form is not found.', 'inbox');
            }

            if (isset($_POST['inbox_form_popup_rules']) && is_array($_POST['inbox_form_popup_rules'])) {
                foreach ($_POST['inbox_form_popup_rules'] as $formRule) {
                    $ruleType = sanitize_text_field($formRule['rule_type'] ?? null);
                    $rulePath = sanitize_text_field($formRule['rule_path'] ?? null);

                    if (! validateRequired($ruleType) || ! validateRequired($rulePath)) {
                        $inboxError = __('Rule field is required.', 'inbox');
                        break;
                    }

                    $formRules[] = ['type' => $ruleType, 'path' => trim($rulePath, '/')];
                }
            }

            if (! isset($inboxError)) {
                $result = FormRepository::createForm([
                    'name' => $formName,
                    'form_id' => sanitize_text_field($inboxForm['id']),
                    'type' => $formType,
                    'url' => esc_url_raw($inboxForm['formUrl']),
                    'selector' => $formPopupSelector,
                    'timeout' => $formPopupTimeout,
                    'days' => $formDays,
                    'rules' => $formRules,
                    'is_active' => $formIsActive
                ]);

                if ($result === false) {
                    $inboxError = __('Form could not be created.', 'inbox');
                } else {
                    wp_cache_delete(INBOX_FORMS_CACHE_KEY);
                    wp_safe_redirect(INBOX_SETTINGS_FORMS_PAGE);
                    exit;
                }
            }
        }

        include(INBOX_ROOT . 'templates/admin/forms_add_new.php');
    }

    /**
     * @return void
     */
    private function adminFormsEditPage(int $id)
    {
        $form = FormRepository::getForm($id);

        if (! $form) {
            wp_safe_redirect(INBOX_SETTINGS_FORMS_PAGE);
            exit;
        }

        $form['rules'] = isset($form['rules']) ? json_decode($form['rules'], true) : [];

        $forms = $this->getInboxForms();

        $selectedForm = (($founds = array_filter($forms, function ($it) use ($form) {
            return $it['id'] == $form['form_id'];
        })) ? array_values($founds)[0] : null);

        if (isset($_POST['save_inbox_form'])) {
            check_admin_referer('wp_nonce', 'wp_nonce');

            unset($inboxError);

            $formName = sanitize_text_field($_POST['inbox_form_name'] ?? null);
            $formId = sanitize_text_field($_POST['inbox_form'] ?? null);
            $formType = isset($_POST['inbox_form_type']) ? absint($_POST['inbox_form_type']) : null;
            $formPopupTimeout = isset($_POST['inbox_form_popup_timeout']) ? absint($_POST['inbox_form_popup_timeout']) : null;
            $formPopupSelector = sanitize_text_field($_POST['inbox_form_popup_selector'] ?? null);
            $formDays = isset($_POST['inbox_form_popup_days']) ? absint($_POST['inbox_form_popup_days']) : null;
            $formIsActive = isset($_POST['inbox_form_is_active']) && $_POST['inbox_form_is_active'];
            $formRules = [];

            if (! validateRequired($formName)) {
                $inboxError = __('Form name field is required.', 'inbox');
            } elseif ($form['name'] != $formName && FormRepository::formExistsByName($formName)) {
                $inboxError = __('Form with similar name exist already.', 'inbox');
            } elseif (! validateRequired($formType) || ! in_array($formType, [
                    FormRepository::TYPE_POPUP, FormRepository::TYPE_INLINE,
                ])) {
                $inboxError = __('Form type is required.', 'inbox');
            } elseif (! validateRequired($formId)) {
                $inboxError = __('Form is required.', 'inbox');
            } elseif (! $inboxForm = (($founds = array_filter($forms, function ($item) use ($formId) {
                return $item['id'] == $formId;
            })) ? array_values($founds)[0] : null)) {
                $inboxError = __('Inbox form is not found.', 'inbox');
            }

            if (isset($_POST['inbox_form_popup_rules']) && is_array($_POST['inbox_form_popup_rules'])) {
                foreach ($_POST['inbox_form_popup_rules'] as $formRule) {
                    $ruleType = sanitize_text_field($formRule['rule_type'] ?? null);
                    $rulePath = sanitize_text_field($formRule['rule_path'] ?? null);

                    if (! validateRequired($ruleType) || ! validateRequired($rulePath)) {
                        $inboxError = __('Rule field is required.', 'inbox');
                        break;
                    }

                    $formRules[] = ['type' => $ruleType, 'path' => $rulePath];
                }
            }

            if (! isset($inboxError)) {
                $result = FormRepository::updateForm($form['id'], [
                    'name' => $formName,
                    'form_id' => sanitize_text_field($inboxForm['id']),
                    'type' => $formType,
                    'url' => esc_url_raw($inboxForm['formUrl']),
                    'selector' => $formPopupSelector,
                    'timeout' => $formPopupTimeout,
                    'days' => $formDays,
                    'rules' => $formRules,
                    'is_active' => $formIsActive,
                ]);

                if ($result === false) {
                    $inboxError = __('Form could not be updated.', 'inbox');
                } else {
                    wp_cache_delete(INBOX_FORMS_CACHE_KEY);
                    wp_safe_redirect(INBOX_SETTINGS_FORMS_PAGE);
                    exit;
                }
            }
        }

        include(INBOX_ROOT . 'templates/admin/forms_edit.php');
    }

    /**
     * @return array
     */
    private function getInboxForms()
    {
        try {
            $formsResponse = $this->apiClient->get(INBOX_API_BASE_URL . '/inbox/v1/forms', ['headers' => ['x-api-key' => $this->getApiKey()]]);
        } catch (\Exception $e) {
            if ($e instanceof RequestException) {
                $formsResponse = $e->getResponse();
            } else {
                $formsResponse = new Response(500);
            }
        }

        $forms = [];

        if ($formsResponse->getStatusCode() >= 200 && $formsResponse->getStatusCode() < 300) {
            $jsonResponse = json_decode($formsResponse->getBody())->resultObject->items;

            if (is_array($jsonResponse)) {
                foreach ($jsonResponse as $item) {
                    $forms[] = (array) $item;
                }
            }
        }

        return $forms;
    }
}
