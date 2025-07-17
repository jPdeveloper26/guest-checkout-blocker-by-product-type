<?php
/**
 * Fired during plugin activation.
 *
 * @package GuestCheckoutBlockerByProductType
 */

/**
 * Activator class.
 */
class GCBPT_Activator {

	/**
	 * Run on plugin activation.
	 */
	public static function activate() {
		// Check for minimum PHP version.
		if ( version_compare( PHP_VERSION, '7.2', '<' ) ) {
			deactivate_plugins( GCBPT_PLUGIN_BASENAME );
			wp_die(
				esc_html__( 'Guest Checkout Blocker by Product Type requires PHP version 7.2 or higher.', 'guest-checkout-blocker-by-product-type' ),
				esc_html__( 'Plugin Activation Error', 'guest-checkout-blocker-by-product-type' ),
				array( 'back_link' => true )
			);
		}

		// Check for minimum WordPress version.
		if ( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
			deactivate_plugins( GCBPT_PLUGIN_BASENAME );
			wp_die(
				esc_html__( 'Guest Checkout Blocker by Product Type requires WordPress version 5.0 or higher.', 'guest-checkout-blocker-by-product-type' ),
				esc_html__( 'Plugin Activation Error', 'guest-checkout-blocker-by-product-type' ),
				array( 'back_link' => true )
			);
		}

		// Set default options if they don't exist.
		if ( false === get_option( 'gcbpt_settings' ) ) {
			$default_settings = array(
				'enabled'        => 'no',
				'product_types'  => array( 'downloadable', 'virtual' ),
				'message'        => __( 'Your cart contains items that require an account. Please log in or create an account to continue.', 'guest-checkout-blocker-by-product-type' ),
				'redirect_page'  => 'my-account',
			);
			add_option( 'gcbpt_settings', $default_settings );
		}

		// Clear the permalinks.
		flush_rewrite_rules();
	}
}