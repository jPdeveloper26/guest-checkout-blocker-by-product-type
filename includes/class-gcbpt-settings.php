<?php
/**
 * Settings management for the plugin.
 *
 * @package GuestCheckoutBlockerByProductType
 */

/**
 * Settings class.
 */
class GCBPT_Settings {

	/**
	 * Get available product types.
	 *
	 * @return array
	 */
	public function get_available_product_types() {
		$types = array(
			'simple'       => __( 'Simple Product', 'guest-checkout-blocker-by-product-type' ),
			'variable'     => __( 'Variable Product', 'guest-checkout-blocker-by-product-type' ),
			'grouped'      => __( 'Grouped Product', 'guest-checkout-blocker-by-product-type' ),
			'external'     => __( 'External/Affiliate Product', 'guest-checkout-blocker-by-product-type' ),
			'virtual'      => __( 'Virtual Product', 'guest-checkout-blocker-by-product-type' ),
			'downloadable' => __( 'Downloadable Product', 'guest-checkout-blocker-by-product-type' ),
		);

		// Allow other plugins to add custom product types.
		return apply_filters( 'gcbpt_available_product_types', $types );
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		$settings = get_option( 'gcbpt_settings', $this->get_default_settings() );
		?>
		<div class="wrap gcbpt-settings-wrap">
			<h1 class="wp-heading-inline">
				<span class="dashicons dashicons-cart"></span>
				<?php echo esc_html( get_admin_page_title() ); ?>
			</h1>
			
			<?php settings_errors( 'gcbpt_messages' ); ?>
			
			<div class="gcbpt-settings-container">
				<div class="gcbpt-main-content">
					<form method="post" action="" class="gcbpt-settings-form">
						<?php wp_nonce_field( 'gcbpt_save_settings', 'gcbpt_settings_nonce' ); ?>
						
						<!-- Status Card -->
						<div class="gcbpt-card gcbpt-status-card">
							<h2><?php esc_html_e( 'Plugin Status', 'guest-checkout-blocker-by-product-type' ); ?></h2>
							<div class="gcbpt-switch-container">
								<label class="gcbpt-switch">
									<input type="checkbox" id="gcbpt_enabled" name="gcbpt_enabled" value="1" <?php checked( $settings['enabled'], 'yes' ); ?> />
									<span class="gcbpt-slider"></span>
								</label>
								<span class="gcbpt-switch-label">
									<?php esc_html_e( 'Enable Guest Checkout Blocker', 'guest-checkout-blocker-by-product-type' ); ?>
								</span>
							</div>
							<p class="description"><?php esc_html_e( 'Enable or disable the guest checkout blocker functionality.', 'guest-checkout-blocker-by-product-type' ); ?></p>
						</div>
						
						<!-- Product Types Card -->
						<div class="gcbpt-card gcbpt-settings-section" data-section="product-types">
							<h2><?php esc_html_e( 'Product Type Restrictions', 'guest-checkout-blocker-by-product-type' ); ?></h2>
							<p class="section-description"><?php esc_html_e( 'Select which product types require customer login/registration during checkout.', 'guest-checkout-blocker-by-product-type' ); ?></p>
							
							<div class="gcbpt-product-types-grid">
								<?php foreach ( $this->get_available_product_types() as $type => $label ) : ?>
									<label class="gcbpt-product-type-item">
										<input type="checkbox" name="gcbpt_product_types[]" value="<?php echo esc_attr( $type ); ?>" 
											<?php checked( in_array( $type, $settings['product_types'], true ) ); ?> />
										<span class="gcbpt-product-type-box">
											<span class="dashicons <?php echo esc_attr( $this->get_product_type_icon( $type ) ); ?>"></span>
											<span class="gcbpt-product-type-label"><?php echo esc_html( $label ); ?></span>
										</span>
									</label>
								<?php endforeach; ?>
							</div>
							
							<div class="gcbpt-select-controls">
								<a href="#" class="button button-small" id="gcbpt-select-all"><?php esc_html_e( 'Select All', 'guest-checkout-blocker-by-product-type' ); ?></a>
								<a href="#" class="button button-small" id="gcbpt-select-none"><?php esc_html_e( 'Clear All', 'guest-checkout-blocker-by-product-type' ); ?></a>
							</div>
						</div>
						
						<!-- Message Customization Card -->
						<div class="gcbpt-card gcbpt-settings-section" data-section="message">
							<h2><?php esc_html_e( 'Customer Notice', 'guest-checkout-blocker-by-product-type' ); ?></h2>
							<p class="section-description"><?php esc_html_e( 'Customize the message shown to guests when their cart contains restricted products.', 'guest-checkout-blocker-by-product-type' ); ?></p>
							
							<div class="gcbpt-message-preview">
								<label><?php esc_html_e( 'Preview:', 'guest-checkout-blocker-by-product-type' ); ?></label>
								<div class="woocommerce-info gcbpt-preview-message">
									<?php echo wp_kses_post( $settings['message'] ); ?>
								</div>
							</div>
							
							<div class="gcbpt-form-field">
								<label for="gcbpt_message"><?php esc_html_e( 'Message Text', 'guest-checkout-blocker-by-product-type' ); ?></label>
								<textarea id="gcbpt_message" name="gcbpt_message" rows="4" class="large-text"><?php echo esc_textarea( $settings['message'] ); ?></textarea>
								<p class="description"><?php esc_html_e( 'You can use basic HTML for formatting. The message will appear at the top of the checkout page.', 'guest-checkout-blocker-by-product-type' ); ?></p>
							</div>
						</div>
						
						<!-- Redirect Settings Card -->
						<div class="gcbpt-card gcbpt-settings-section" data-section="redirect">
							<h2><?php esc_html_e( 'Redirect Settings', 'guest-checkout-blocker-by-product-type' ); ?></h2>
							<p class="section-description"><?php esc_html_e( 'Configure where guests should be redirected when trying to checkout with restricted products.', 'guest-checkout-blocker-by-product-type' ); ?></p>
							
							<div class="gcbpt-form-field">
								<label for="gcbpt_redirect_page"><?php esc_html_e( 'Redirect Destination', 'guest-checkout-blocker-by-product-type' ); ?></label>
								<select id="gcbpt_redirect_page" name="gcbpt_redirect_page" class="regular-text">
									<option value="my-account" <?php selected( $settings['redirect_page'], 'my-account' ); ?>>
										<?php esc_html_e( 'My Account Page', 'guest-checkout-blocker-by-product-type' ); ?>
									</option>
									<option value="login" <?php selected( $settings['redirect_page'], 'login' ); ?>>
										<?php esc_html_e( 'WordPress Login Page', 'guest-checkout-blocker-by-product-type' ); ?>
									</option>
								</select>
								<p class="description"><?php esc_html_e( 'Choose where to send guests who need to create an account.', 'guest-checkout-blocker-by-product-type' ); ?></p>
							</div>
						</div>
						
						<div class="gcbpt-save-container">
							<?php submit_button( __( 'Save Settings', 'guest-checkout-blocker-by-product-type' ), 'primary large' ); ?>
						</div>
					</form>
				</div>
				
				<!-- Sidebar -->
				<div class="gcbpt-sidebar">
					<div class="gcbpt-card">
						<h3><?php esc_html_e( 'Quick Stats', 'guest-checkout-blocker-by-product-type' ); ?></h3>
						<ul class="gcbpt-stats">
							<li>
								<span class="dashicons dashicons-yes-alt"></span>
								<?php
								printf(
									/* translators: %s: enabled/disabled status */
									esc_html__( 'Status: %s', 'guest-checkout-blocker-by-product-type' ),
									'yes' === $settings['enabled'] ? '<strong>' . esc_html__( 'Enabled', 'guest-checkout-blocker-by-product-type' ) . '</strong>' : '<strong>' . esc_html__( 'Disabled', 'guest-checkout-blocker-by-product-type' ) . '</strong>'
								);
								?>
							</li>
							<li>
								<span class="dashicons dashicons-cart"></span>
								<?php
								printf(
									/* translators: %d: number of restricted product types */
									esc_html__( 'Restricted Types: %d', 'guest-checkout-blocker-by-product-type' ),
									count( $settings['product_types'] )
								);
								?>
							</li>
						</ul>
					</div>
					
					<div class="gcbpt-card" style="display:none;">
						<h3><?php esc_html_e( 'Need Help?', 'guest-checkout-blocker-by-product-type' ); ?></h3>
						<p><?php esc_html_e( 'Check out our documentation for detailed instructions and troubleshooting.', 'guest-checkout-blocker-by-product-type' ); ?></p>
						<a href="#" class="button button-secondary"><?php esc_html_e( 'View Documentation', 'guest-checkout-blocker-by-product-type' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get icon for product type.
	 *
	 * @param string $type Product type.
	 * @return string Dashicon class.
	 */
	private function get_product_type_icon( $type ) {
		$icons = array(
			'simple'       => 'dashicons-tag',
			'variable'     => 'dashicons-networking',
			'grouped'      => 'dashicons-category',
			'external'     => 'dashicons-external',
			'virtual'      => 'dashicons-cloud',
			'downloadable' => 'dashicons-download',
		);
		
		return isset( $icons[ $type ] ) ? $icons[ $type ] : 'dashicons-tag';
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
			'message'        => __( 'Your cart contains items that require an account. Please log in or create an account to continue.', 'guest-checkout-blocker-by-product-type' ),
			'redirect_page'  => 'my-account',
		);
	}
}