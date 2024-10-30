<?php defined('ABSPATH') or die(); ?>

<?php include('header.php'); ?>

<div class="wrap">
    <h2 class="wp-csa-heading"><?php echo esc_html__('Settings', 'inbox') ?></h2>
    <?php include('error.php') ?>
    <div id="poststuff" class="wp_csa_view">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="inbox-settings-wrap" data-option-name="<?php echo esc_attr(INBOX_SETTINGS_DB_OPTION_NAME) ?>">
                    <div class="metabox-holder inbox-tab-settings">
                        <form method="post">
                            <input id="wp_nonce" type="hidden" name="wp_nonce"
                                   value="<?php echo wp_create_nonce('wp_nonce') ?>">
                            <div id="general_settings" class="inbox-group-wrapper" style="">
                                <div class="postbox">
                                    <div class="postbox-header">
                                        <h3 class="hndle is-non-sortable">
                                            <span><?php echo esc_html__('API', 'inbox') ?></span>
                                        </h3>
                                    </div>
                                    <div class="inside">
                                        <table class="form-table">
                                            <tbody>
                                            <tr id="inbox_api_key_row">
                                                <th scope="row">
                                                    <label for="inbox_api_key"><?php echo esc_html__('Enter API Key', 'inbox') ?></label>
                                                </th>
                                                <td>
                                                    <input type="text" id="inbox_api_key"
                                                           name="inbox_api_key"
                                                           class="regular-text"
                                                           value="<?php echo esc_attr($apiKey) ?>">
                                                    <p class="description"><?php echo sprintf(esc_html__('Log in to your %s INBOX account %s to get your API Key.', 'inbox'), '<a target="_blank"
                                                                                             href="https://accounts.useinbox.com/api-keys">', '</a>') ?></p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <p><input class="button-primary" type="submit" name="save_inbox_settings"
                                                  value="<?php echo esc_attr__('Save Changes', 'inbox') ?>"></p>
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
