<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package GuestCheckoutBlockerByProductType
 */

/**
 * Admin class.
 */
class GCBPT_Admin {

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
	 * Settings instance.
	 *
	 * @var GCBPT_Settings
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = new GCBPT_Settings();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_styles( $hook ) {
		if ( 'woocommerce_page_gcbpt-settings' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name,
			GCBPT_PLUGIN_URL . 'assets/css/gcbpt-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'woocommerce_page_gcbpt-settings' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name,
			GCBPT_PLUGIN_URL . 'assets/js/gcbpt-admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'gcbpt_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'gcbpt_admin_nonce' ),
			)
		);
	}

	/**
	 * Add admin menu.
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'woocommerce',
			esc_html__( 'Guest Checkout Blocker', 'guest-checkout-blocker-by-product-type' ),
			esc_html__( 'Guest Checkout Blocker', 'guest-checkout-blocker-by-product-type' ),
			'manage_woocommerce',
			'gcbpt-settings',
			array( $this, 'display_settings_page' )
		);
	}

	/**
	 * Display settings page.
	 */
	public function display_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Check if form was submitted with nonce verification.
		if ( isset( $_POST['gcbpt_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcbpt_settings_nonce'] ) ), 'gcbpt_save_settings' ) ) {
			$this->save_settings();
		}

		$this->settings->render_settings_page();
	}

	/**
	 * Save settings.
	 */
	private function save_settings() {
		// Verify nonce.
		if ( ! isset( $_POST['gcbpt_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcbpt_settings_nonce'] ) ), 'gcbpt_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed', 'guest-checkout-blocker-by-product-type' ) );
		}

		$settings = array(
			'enabled'        => isset( $_POST['gcbpt_enabled'] ) ? 'yes' : 'no',
			'product_types'  => isset( $_POST['gcbpt_product_types'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['gcbpt_product_types'] ) ) : array(),
			'message'        => isset( $_POST['gcbpt_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['gcbpt_message'] ) ) : '',
			'redirect_page'  => isset( $_POST['gcbpt_redirect_page'] ) ? sanitize_text_field( wp_unslash( $_POST['gcbpt_redirect_page'] ) ) : 'my-account',
		);

		update_option( 'gcbpt_settings', $settings );

		add_settings_error(
			'gcbpt_messages',
			'gcbpt_message',
			esc_html__( 'Settings saved successfully.', 'guest-checkout-blocker-by-product-type' ),
			'updated'
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			'gcbpt_settings_group',
			'gcbpt_settings',
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $settings Settings array.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $settings ) {
		$sanitized = array();
		
		$sanitized['enabled'] = isset( $settings['enabled'] ) && 'yes' === $settings['enabled'] ? 'yes' : 'no';
		$sanitized['product_types'] = isset( $settings['product_types'] ) && is_array( $settings['product_types'] ) ? array_map( 'sanitize_text_field', $settings['product_types'] ) : array();
		$sanitized['message'] = isset( $settings['message'] ) ? sanitize_textarea_field( $settings['message'] ) : '';
		$sanitized['redirect_page'] = isset( $settings['redirect_page'] ) ? sanitize_text_field( $settings['redirect_page'] ) : 'my-account';
		
		return $sanitized;
	}
}