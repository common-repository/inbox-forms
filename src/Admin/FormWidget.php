<?php

namespace Inbox\Admin;

use Inbox\Repositories\FormRepository;
use WP_Widget;

if (! class_exists('WP_Widget')) {
    require_once(ABSPATH . 'wp-includes/class-wp-widget.php');
}

class FormWidget extends WP_Widget
{
    private static $instance;

    public function __construct()
    {
        parent::__construct(
            'inbox_forms_widget',
            'INBOX Form',
            [
                'description' => __('INBOX Form Widget', 'inbox'),
            ]
        );

        add_action('widgets_init', [$this, 'registerWidget']);
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function widget($args, $instance)
    {
        $form_id = isset($instance['inbox_form_id']) && intval($instance['inbox_form_id']) ? $instance['inbox_form_id'] : null;

        if (! $form = FormRepository::getForm($form_id)) {
            return;
        }

        $inboxForm = new InboxForm();

        $inboxForm->generate($form);
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['inbox_form_id'] = (! empty($new_instance['inbox_form_id'])) ? strip_tags($new_instance['inbox_form_id']) : null;

        return $instance;
    }

    public function form($instance)
    {
        $forms = FormRepository::getForms();

        $id = $instance['inbox_form_id'] ?? null;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('inbox_form_id')); ?>"><?php echo esc_html__('Select form:', 'inbox'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('inbox_form_id')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('inbox_form_id')); ?>">
                <option value=""><?php echo esc_html__('Select...', 'inbox') ?></option>
                <?php foreach ($forms as $form) { ?>
                    <option value="<?php echo esc_attr($form['id']); ?>"<?php echo $form['id'] == $id ? ' selected="selected"' : ''; ?>><?php echo esc_html($form['name']); ?></option>
                <?php } ?>
            </select>
        </p>
        <?php
    }

    public function registerWidget()
    {
        register_widget($this);
    }
}
?>
