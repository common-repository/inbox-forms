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
                                            <span><?php echo esc_html__('Add New Form', 'inbox') ?></span>
                                        </h3>
                                    </div>
                                    <div class="inside">
                                        <table class="form-table">
                                            <tbody>
                                            <tr id="inbox_form_name_row">
                                                <th scope="row">
                                                    <label for="inbox_form_name"><?php echo esc_html__('Form Name', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input type="text" id="inbox_form_name" name="inbox_form_name" class="regular-text" value="">
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_row">
                                                <th scope="row">
                                                    <label for="inbox_form"><?php echo esc_html__('INBOX Form', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <select class="regular-text" name="inbox_form" id="inbox_form">
                                                        <?php foreach ($forms as $form) { ?>
                                                            <option value="<?php echo esc_attr($form['id']) ?>" data-url="<?php echo esc_url($form['formUrl']) ?>"><?php echo esc_html($form['title']) ?></option>
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
                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_INLINE) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getTypeName(\Inbox\Repositories\FormRepository::TYPE_INLINE)) ?></option>
                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getTypeName(\Inbox\Repositories\FormRepository::TYPE_POPUP)) ?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_is_active_row">
                                                <th scope="row">
                                                    <label for="inbox_form_is_active"><?php echo esc_html__('Active?', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input type="checkbox" id="inbox_form_is_active" name="inbox_form_is_active" value="1" checked>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_timeout_row" class="hidden" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label for="inbox_form_popup_timeout"><?php echo esc_html__('Popup Timeout', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input class="regular-text" name="inbox_form_popup_timeout" id="inbox_form_popup_timeout" type="number">
                                                    <p class="description"><?php echo esc_html__('Wait time before showing popup in milliseconds. For example, you can write 5000 milliseconds for 5 seconds.', 'inbox') ?></p>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_selector_row" class="hidden" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label for="inbox_form_popup_selector"><?php echo esc_html__('Popup Selector', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input class="regular-text" name="inbox_form_popup_selector" id="inbox_form_popup_selector" type="text">
                                                    <p class="description"><?php echo esc_html__('You can fill this if you want show popup when click to selector. Selector must be valid javascript dom selector.', 'inbox') ?></p>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_days_row" class="hidden" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label for="inbox_form_popup_days"><?php echo esc_html__('Popup Show Days', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input class="regular-text" name="inbox_form_popup_days" id="inbox_form_popup_days" type="number">
                                                    <p class="description"><?php echo esc_html__('You can fill this if you want show popup once a every specified days.', 'inbox') ?></p>
                                                </td>
                                            </tr>
                                            <tr id="inbox_form_popup_rules_row" class="hidden" data-form-type="<?php echo esc_attr(\Inbox\Repositories\FormRepository::TYPE_POPUP) ?>">
                                                <th scope="row">
                                                    <label><?php echo esc_html__('Auto Popup Rules', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <div data-repeater-list="inbox_form_popup_rules" data-repeater-init-empty="true">
                                                        <div data-repeater-item style="margin-bottom: 10px;">
                                                            <div style="display: flex; align-items: flex-end; justify-content: space-between;">
                                                                <label for="rule_type">
                                                                    <span style="margin-bottom: 5px;"><?php echo esc_html__('Rule', 'inbox') ?></span>
                                                                    <select class="regular-text" name="rule_type">
                                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_CONTAIN) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_CONTAIN)) ?></option>
                                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_CONTAIN) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_CONTAIN)) ?></option>
                                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_EQUAL) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_EQUAL)) ?></option>
                                                                        <option value="<?php echo esc_attr(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_EQUAL) ?>"><?php echo esc_html(\Inbox\Repositories\FormRepository::getRuleTypeName(\Inbox\Repositories\FormRepository::RULE_TYPE_NOT_EQUAL)) ?></option>
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
                                                        <iframe src="<?php echo isset($forms[0]) ? esc_url($forms[0]['formUrl']) : '' ?>" frameborder="0" style="width: 100%; height: 400px; display: <?php echo isset($forms[0]) ? 'inherit' : 'none' ?>>"></iframe>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <p>
                                            <input class="button-primary" type="submit" name="save_inbox_form" value="<?php echo esc_attr__('Add', 'inbox') ?>">
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
