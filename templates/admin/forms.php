<?php defined('ABSPATH') or die(); ?>

<?php include('header.php'); ?>

<div class="inbox-data-listing">
    <div class="wrap">
        <h2 class="wp-csa-heading">
            <?php echo esc_html__('Forms', 'inbox') ?>
            <a class="add-new-h2" href="<?php echo add_query_arg('view', 'add-new', INBOX_SETTINGS_FORMS_PAGE) ?>"><?php echo esc_html__('Add New', 'inbox') ?></a>
        </h2>
        <?php include('error.php') ?>
        <div id="poststuff" class="wp_csa_view">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <?php do_action('wp_ib_main_content_area'); ?>
                </div>
                <?php include('sidebar.php') ?>
            </div>
        </div>
    </div>
</div>
