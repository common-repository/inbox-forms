<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

include_once( dirname( __FILE__ ) . '/inbox-form.php' );

function inbox_ib_uninstall_function() {
	$remove_plugin_data = false;

	if ( $remove_plugin_data == 'true' ) {

		wp_cache_flush();
	}
}

if ( ! is_multisite() ) {
	inbox_ib_uninstall_function();
} else {
	if ( ! wp_is_large_network() ) {
		$site_ids = get_sites( [ 'fields' => 'ids', 'number' => 0 ] );

		foreach ( $site_ids as $site_id ) {
			switch_to_blog( $site_id );
			inbox_ib_uninstall_function();
			restore_current_blog();
		}
	}
}
