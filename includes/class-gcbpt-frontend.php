<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package GuestCheckoutBlockerByProductType
 */

/**
 * Frontend class.
 */
class GCBPT_Frontend {

	/**
	 * The ID of this plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		
		// Enqueue frontend styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Register the stylesheets for the public-facing side.
	 */
	public function enqueue_styles() {
		if ( is_checkout() || is_cart() ) {
			wp_enqueue_style(
				$this->plugin_name,
				GCBPT_PLUGIN_URL . 'assets/css/gcbpt-frontend.css',
				array(),
				$this->version,
				'all'
			);
		}
	}

	/**
	 * Force registration for specific product types.
	 *
	 * @param bool $registration_required Whether registration is required.
	 * @return bool
	 */
	public function force_registration_for_product_types( $registration_required ) {
		// Get plugin settings.
		$settings = get_option( 'gcbpt_settings', $this->get_default_settings() );

		// Check if plugin is enabled.
		if ( 'yes' !== $settings['enabled'] ) {
			return $registration_required;
		}

		// Check if we're on checkout page.
		if ( ! is_checkout() || is_wc_endpoint_url() ) {
			return $registration_required;
		}

		// Check cart items.
		if ( WC()->cart && ! WC()->cart->is_empty() ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$product = $cart_item['data'];
				
				if ( $this->is_restricted_product_type( $product, $settings['product_types'] ) ) {
					return true;
				}
			}
		}

		return $registration_required;
	}

	/**
	 * Check if product is of restricted type.
	 *
	 * @param WC_Product $product       Product object.
	 * @param array      $product_types Restricted product types.
	 * @return bool
	 */
	private function is_restricted_product_type( $product, $product_types ) {
		if ( ! $product || empty( $product_types ) ) {
			return false;
		}

		$product_type = $product->get_type();

		// Check for virtual and downloadable products.
		if ( in_array( 'virtual', $product_types, true ) && $product->is_virtual() ) {
			return true;
		}

		if ( in_array( 'downloadable', $product_types, true ) && $product->is_downloadable() ) {
			return true;
		}

		// Check for specific product type.
		if ( in_array( $product_type, $product_types, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Display login notice on checkout.
	 */
	public function display_login_notice() {
		// Get plugin settings.
		$settings = get_option( 'gcbpt_settings', $this->get_default_settings() );

		// Check if plugin is enabled.
		if ( 'yes' !== $settings['enabled'] ) {
			return;
		}

		// Check if user is logged in.
		if ( is_user_logged_in() ) {
			return;
		}

		// Check if cart contains restricted products.
		$has_restricted_products = false;
		if ( WC()->cart && ! WC()->cart->is_empty() ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$product = $cart_item['data'];
				
				if ( $this->is_restricted_product_type( $product, $settings['product_types'] ) ) {
					$has_restricted_products = true;
					break;
				}
			}
		}

		if ( $has_restricted_products ) {
			$message = ! empty( $settings['message'] ) ? $settings['message'] : $this->get_default_message();
			?>
			<div class="woocommerce-info gcbpt-login-notice">
				<?php echo wp_kses_post( $message ); ?>
			</div>
			<?php
		}
	}

	/**
	 * Modify checkout fields based on restricted products.
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public function modify_checkout_fields( $fields ) {
		// Get plugin settings.
		$settings = get_option( 'gcbpt_settings', $this->get_default_settings() );

		// Check if plugin is enabled.
		if ( 'yes' !== $settings['enabled'] ) {
			return $fields;
		}

		// Check if user is logged in.
		if ( is_user_logged_in() ) {
			return $fields;
		}

		// Check if cart contains restricted products.
		$has_restricted_products = false;
		if ( WC()->cart && ! WC()->cart->is_empty() ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$product = $cart_item['data'];
				
				if ( $this->is_restricted_product_type( $product, $settings['product_types'] ) ) {
					$has_restricted_products = true;
					break;
				}
			}
		}

		if ( $has_restricted_products ) {
			// Make account fields required.
			if ( isset( $fields['account'] ) ) {
				$fields['account']['account_username']['required'] = true;
				$fields['account']['account_password']['required'] = true;
			}
		}

		return $fields;
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	private function get_default_settings() {
		return array(
			'enabled'        => 'no',
			'product_types'  => array(),
			'message'        => $this->get_default_message(),
			'redirect_page'  => 'my-account',
		);
	}

	/**
	 * Get default message.
	 *
	 * @return string
	 */
	private function get_default_message() {
		return __( 'Your cart contains items that require an account. Please log in or create an account to continue.', 'guest-checkout-blocker-by-product-type' );
	}
}