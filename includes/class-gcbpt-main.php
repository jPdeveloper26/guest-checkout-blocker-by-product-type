<?php
/**
 * The core plugin class.
 *
 * @package GuestCheckoutBlockerByProductType
 */

/**
 * Main plugin class.
 */
class GCBPT_Main {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @var object
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		$this->version     = GCBPT_VERSION;
		$this->plugin_name = 'guest-checkout-blocker-by-product-type';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_frontend_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once GCBPT_PLUGIN_DIR . 'includes/class-gcbpt-admin.php';
		require_once GCBPT_PLUGIN_DIR . 'includes/class-gcbpt-frontend.php';
		require_once GCBPT_PLUGIN_DIR . 'includes/class-gcbpt-settings.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'guest-checkout-blocker-by-product-type',
			false,
			dirname( GCBPT_PLUGIN_BASENAME ) . '/languages/'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new GCBPT_Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $plugin_admin, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $plugin_admin, 'register_settings' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_frontend_hooks() {
		$plugin_frontend = new GCBPT_Frontend( $this->get_plugin_name(), $this->get_version() );

		add_filter( 'woocommerce_checkout_registration_required', array( $plugin_frontend, 'force_registration_for_product_types' ) );
		add_action( 'woocommerce_before_checkout_form', array( $plugin_frontend, 'display_login_notice' ) );
		add_filter( 'woocommerce_checkout_fields', array( $plugin_frontend, 'modify_checkout_fields' ) );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		// All hooks are already registered in the constructor.
	}

	/**
	 * The name of the plugin.
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}