<?php

namespace Inbox\Admin;

use Inbox\Repositories\FormRepository;

class InboxForm
{
    public function generate($form)
    {
        if (! $form || empty($form['url']) || ! $form['is_active']) {
            return;
        }

        if ($form['type'] == FormRepository::TYPE_INLINE) {
            ?>
            <iframe id="inbox_form_iframe" width="540" height="305" src="<?php echo esc_url($form['url']) ?>" allowfullscreen
                    style="max-width: 100%; border: 0;"></iframe>
            <?php
        } else {
            ?>
            <div id="inbox_form_popup" data-inbox-popup='<?php echo json_encode(array_intersect_key($form, array_flip([
                'id', 'url', 'selector', 'timeout', 'days',
            ]))) ?>' style="display: none"></div>
            <?php
        }
    }
}
