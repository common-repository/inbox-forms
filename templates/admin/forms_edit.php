<?php defined('ABSPATH') or die(); ?>

<?php include('header.php'); ?>

<div class="wrap">
    <h2 class="wp-csa-heading">
        <?php echo esc_html__('Forms', 'inbox') ?>
        <a class="add-new-h2" href="<?php echo esc_url(INBOX_SETTINGS_FORMS_PAGE) ?>"><?php echo esc_html__('Back to Overview', 'inbox') ?></a>
    </h2>
    <?php include('error.php') ?>
    <div id="poststuff" class="wp_csa_view">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="inbox-settings-wrap">
                    <div class="metabox-holder inbox-tab-settings">
                        <form method="post">
                            <input id="wp_nonce" type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce('wp_nonce') ?>">
                            <div id="general_settings" class="inbox-group-wrapper" style="">
                                <div class="postbox">
                                    <div class="postbox-header">
                                        <h3 class="hndle is-non-sortable">
                                            <span><?php echo esc_html__('Edit Form', 'inbox') ?></span>
                                        </h3>
                                    </div>
                                    <div class="inside">
                                        <div class="notice notice-info">
                                            <p>
                                                <?php echo sprintf(esc_html__('Use the shortcode %s to display this form inside a post, page or text widget.', 'inbox'), '<input type="text" onfocus="this.select();" readonly="readonly" value="[inbox_form form_id=' . esc_attr($form['id']) . ']" size="26">') ?>
                                            </p>
                                        </div>
                                        <table class="form-table">
                                            <tbody>
                                            <tr id="inbox_form_name_row">
                                                <th scope="row">
                                                    <label for="inbox_form_name"><?php echo esc_html__('Form Name', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input type="text" id="inbox_form_name" name="inbox_form_name" class="regular-text" value="<?php echo esc_attr($form['name']) ?>">
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_row">
                                                <th scope="row">
                                                    <label for="inbox_form"><?php echo esc_html__('INBOX Form', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <select class="regular-text" name="inbox_form" id="inbox_form">
                                                        <?php foreach ($forms as $iform) { ?>
                                                            <option value="<?php echo esc_attr($iform['id']) ?>" data-url="<?php echo esc_url($iform['formUrl']) ?>"<?php echo $form['form_id'] == $iform['id'] ? ' selected' : '' ?>><?php echo esc_html($iform['title']) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_type_row">
                                                <th scope="row">
                                                    <label for="inbox_form_type"><?php echo esc_html__('Form Type', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <select class="regular-text" name="inbox_form_type" id="inbox_form_type">
                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_INLINE) ?>"<?php echo $form['type'] == \Inbox\Repositories\FormRepository::TYPE_INLINE ? ' selected' : '' ?>><?php echo esc_html(\Inbox\Repositories\FormRepository::getTypeName(\Inbox\Repositories\FormRepository::TYPE_INLINE)) ?></option>
                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>"<?php echo $form['type'] == \Inbox\Repositories\FormRepository::TYPE_POPUP ? ' selected' : '' ?>><?php echo esc_html(\Inbox\Repositories\FormRepository::getTypeName(\Inbox\Repositories\FormRepository::TYPE_POPUP)) ?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_is_active_row">
                                                <th scope="row">
                                                    <label for="inbox_form_is_active"><?php echo esc_html__('Active?', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input type="checkbox" id="inbox_form_is_active" name="inbox_form_is_active" value="1"<?php echo $form['is_active'] ? ' checked' : '' ?>>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_timeout_row" class="<?php echo $form['type'] != \Inbox\Repositories\FormRepository::TYPE_POPUP ? 'hidden' : '' ?>" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label for="inbox_form_popup_timeout"><?php echo esc_html__('Popup Timeout', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input class="regular-text" name="inbox_form_popup_timeout" id="inbox_form_popup_timeout" type="number" value="<?php echo esc_attr($form['timeout']) ?>">
                                                    <p class="description"><?php echo esc_html__('Wait time before showing popup in milliseconds. For example, you can write 5000 milliseconds for 5 seconds.', 'inbox') ?></p>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_selector_row" class="<?php echo $form['type'] != \Inbox\Repositories\FormRepository::TYPE_POPUP ? 'hidden' : '' ?>" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label for="inbox_form_popup_selector"><?php echo esc_html__('Popup Selector', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input class="regular-text" name="inbox_form_popup_selector" id="inbox_form_popup_selector" type="text" value="<?php echo esc_attr($form['selector']) ?>">
                                                    <p class="description"><?php echo esc_html__('You can fill this if you want show popup when click to selector. Selector must be valid javascript dom selector.', 'inbox') ?></p>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_days_row" class="<?php echo $form['type'] != \Inbox\Repositories\FormRepository::TYPE_POPUP ? 'hidden' : '' ?>" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label for="inbox_form_popup_days"><?php echo esc_html__('Popup Show Days', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input class="regular-text" name="inbox_form_popup_days" id="inbox_form_popup_days" type="number" value="<?php echo esc_attr($form['days']) ?>">
                                                    <p class="description"><?php echo esc_html__('You can fill this if you want show popup once a every specified days.', 'inbox') ?></p>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_rules_row" class="<?php echo $form['type'] != \Inbox\Repositories\FormRepository::TYPE_POPUP ? 'hidden' : '' ?>" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label><?php echo esc_html__('Auto Popup Rules', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <div data-repeater-list="inbox_form_popup_rules" data-repeater-init-empty="<?php echo count($form['rules']) ? 'false' : 'true' ?>">
                                                        <?php if (count($form['rules'])) { ?>
                                                            <?php foreach ($form['rules'] as $rule) { ?>
                                                                <div data-repeater-item style="margin-bottom: 10px;">
                                                                    <div style="display: flex; align-items: flex-end; justify-content: space-between;">
                                                                        <label for="rule_type">
                                                                            <span style="margin-bottom: 5px;"><?php echo esc_html__('Rule', 'inbox') ?></span>
                                                                            <select class="regular-text" name="rule_type">
                                                                                <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_CONTAIN) ?>"<?php echo $rule['type'] == \Inbox\Repositories\FormRepository::RULE_TYPE_CONTAIN ? ' selected' : '' ?>><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_CONTAIN)) ?></option>
                                                                                <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_CONTAIN) ?>"<?php echo $rule['type'] == \Inbox\Repositories\FormRepository::RULE_TYPE_NOT_CONTAIN ? ' selected' : '' ?>><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_CONTAIN)) ?></option>
                                                                                <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_EQUAL) ?>"<?php echo $rule['type'] == \Inbox\Repositories\FormRepository::RULE_TYPE_EQUAL ? ' selected' : '' ?>><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_EQUAL)) ?></option>
                                                                                <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_EQUAL) ?>"<?php echo $rule['type'] == \Inbox\Repositories\FormRepository::RULE_TYPE_NOT_EQUAL ? ' selected' : '' ?>><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_EQUAL)) ?></option>
                                                                            </select>
                                                                        </label>
                                                                        <label for="rule_path">
                                                                            <span style="margin-bottom: 5px;"><?php echo esc_html__('Path', 'inbox') ?></span>
                                                                            <input class="regular-text" name="rule_path" type="text" value="<?php echo esc_attr($rule['path']) ?>">
                                                                        </label>
                                                                        <a href="javascript:;" data-repeater-delete="" class="button-secondary">
                                                                            <?php echo esc_html__('Remove', 'inbox') ?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <div data-repeater-item style="margin-bottom: 10px;">
                                                                <div style="display: flex; align-items: flex-end; justify-content: space-between;">
                                                                    <label for="rule_type">
                                                                        <span style="margin-bottom: 5px;"><?php echo esc_html__('Rule', 'inbox') ?></span>
                                                                        <select class="regular-text" name="rule_type">
                                                                            <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_CONTAIN) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_CONTAIN)) ?></option>
                                                                            <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_EQUAL) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_EQUAL)) ?></option>
                                                                        </select>
                                                                    </label>
                                                                    <label for="rule_path">
                                                                        <span style="margin-bottom: 5px;"><?php echo esc_html__('Path', 'inbox') ?></span>
                                                                        <input class="regular-text" name="rule_path" type="text">
                                                                    </label>
                                                                    <a href="javascript:;" data-repeater-delete="" class="button-secondary">
                                                                        <?php echo esc_html__('Remove', 'inbox') ?>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div style="margin-top: 10px">
                                                        <a href="javascript:;" data-repeater-create="" class="button-primary">
                                                            <?php echo esc_html__('Add', 'inbox') ?>
                                                        </a>
                                                    </div>
                                                    <p class="description"><?php echo esc_html__('You can add a rule if you want show popup on pages that match the rule.', 'inbox') ?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div id="inbox_form_preview">
                                                        <iframe src="<?php echo $selectedForm ? esc_url($selectedForm['formUrl']) : (isset($forms[0]) ? esc_url($forms[0]['formUrl']) : '') ?>" frameborder="0" style="width: 100%; height: 400px; display: <?php echo $selectedForm || isset($forms[0]) ? 'inherit' : 'none' ?>>"></iframe>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <p>
                                            <input class="button-primary" type="submit" name="save_inbox_form" value="<?php echo esc_attr__('Edit', 'inbox') ?>">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include('sidebar.php') ?>
        </div>
    </div>
</div>
