<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package GuestCheckoutBlockerByProductType
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'gcbpt_settings' );

// Clear any cached data that has been removed.
wp_cache_flush();