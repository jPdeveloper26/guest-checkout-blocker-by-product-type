<?php
/**
 * Plugin Name: Guest Checkout Blocker by Product Type for Woocommerce
 * Description: Force login or registration for certain product types in WooCommerce
 * Version: 1.3.0
 * Author: Juan Mojica
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: guest-checkout-blocker-by-product-type
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 8.5
 *
 * @package GuestCheckoutBlockerByProductType
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'gcbcpw_wpbay_sdk' ) ) {
    function gcbcpw_wpbay_sdk() {
        require_once dirname( __FILE__ ) . '/wpbay-sdk/WPBay_Loader.php';
        $sdk_instance = false;
        global $wpbay_sdk_latest_loader;
        $sdk_loader_class = $wpbay_sdk_latest_loader;
        $sdk_params = array(
            'api_key'                 => 'OIAKDA-LTRHGZK4VP5ZXK3DECZI2OJACI',
            'wpbay_product_id'        => '', 
            'product_file'            => __FILE__,
            'activation_redirect'     => '',
            'is_free'                 => true,
            'is_upgradable'           => false,
            'uploaded_to_wp_org'      => false,
            'disable_feedback'        => false,
            'disable_support_page'    => false,
            'disable_contact_form'    => false,
            'disable_upgrade_form'    => false,
            'disable_analytics'       => true,
            'rating_notice'           => '1 week',
            'debug_mode'              => 'false',
            'no_activation_required'  => false,
            'menu_data'               => array(
                'menu_slug' => ''
            ),
        );
        if ( class_exists( $sdk_loader_class ) ) {
            $sdk_instance = $sdk_loader_class::load_sdk( $sdk_params );
        }
        return $sdk_instance;
    }
    gcbcpw_wpbay_sdk();
    do_action( 'gcbcpw_wpbay_sdk_loaded' );
}
// Declare HPOS compatibility
add_action('before_woocommerce_init', function() {
		if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
		}
});

// Define plugin constants.
define( 'GCBPT_VERSION', '1.0.0' );
define( 'GCBPT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GCBPT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GCBPT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function gcbpt_activate() {
	require_once GCBPT_PLUGIN_DIR . 'includes/class-gcbpt-activator.php';
	GCBPT_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function gcbpt_deactivate() {
	// Currently no deactivation tasks required.
}

register_activation_hook( __FILE__, 'gcbpt_activate' );
register_deactivation_hook( __FILE__, 'gcbpt_deactivate' );

/**
 * The core plugin class.
 */
require GCBPT_PLUGIN_DIR . 'includes/class-gcbpt-main.php';

/**
 * Begins execution of the plugin.
 */
function gcbpt_run() {
	$plugin = new GCBPT_Main();
	$plugin->run();
}

// Check if WooCommerce is active.
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	gcbpt_run();
} else {
	add_action( 'admin_notices', 'gcbpt_woocommerce_missing_notice' );
}

/**
 * Display notice if WooCommerce is not active.
 */
function gcbpt_woocommerce_missing_notice() {
	?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'Guest Checkout Blocker by Product Type requires WooCommerce to be installed and active.', 'guest-checkout-blocker-by-product-type' ); ?></p>
	</div>
	<?php
}
