<?php

namespace Inbox\Admin;

use Inbox\Repositories\FormRepository;

if (! defined('ABSPATH')) {
    exit;
}

class FormShortcode
{
    private static $instance;

    public function __construct()
    {
        add_shortcode('inbox_form', [$this, 'formShortcode']);
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function formShortcode($attributes)
    {
        $form_attributes = shortcode_atts([
            'form_id' => '1',
        ], $attributes);

        if (! $form = FormRepository::getForm($form_attributes['form_id'])) {
            return '';
        }

        $inboxForm = new InboxForm();

        ob_start();

        $inboxForm->generate($form);

        return ob_get_clean();
    }
}
