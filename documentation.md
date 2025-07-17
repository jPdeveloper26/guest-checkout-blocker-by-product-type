# Guest Checkout Blocker by Product Type for Woocommerce- Documentation

## Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Usage](#usage)
5. [Developer Documentation](#developer-documentation)
6. [Hooks and Filters](#hooks-and-filters)
7. [Troubleshooting](#troubleshooting)
8. [Coding Standards](#coding-standards)

## Overview

Guest Checkout Blocker by Product Type is a WooCommerce extension that allows store owners to require customer registration or login for specific product types. This is particularly useful for digital products, services, or any products where customer accounts are necessary.

### Key Features

- Selective product type restrictions
- Customizable customer notifications
- Seamless WooCommerce integration
- Translation ready
- Developer-friendly with hooks and filters

## Installation

### Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.2 or higher

### Installation Steps

1. **Upload Plugin**
   - Download the plugin ZIP file
   - Navigate to WordPress Admin > Plugins > Add New
   - Click "Upload Plugin" and select the ZIP file
   - Click "Install Now"

2. **Activate Plugin**
   - After installation, click "Activate Plugin"
   - Ensure WooCommerce is active

3. **Initial Configuration**
   - Navigate to WooCommerce > Guest Checkout Blocker
   - Configure your preferred settings

## Configuration

### Settings Page

Access the settings at **WooCommerce > Guest Checkout Blocker**

#### Available Settings

1. **Enable Guest Checkout Blocker**
   - Toggle to enable/disable the functionality
   - Default: Disabled

2. **Product Types**
   - Select which product types require registration
   - Options:
     - Simple Product
     - Variable Product
     - Grouped Product
     - External/Affiliate Product
     - Virtual Product
     - Downloadable Product

3. **Notice Message**
   - Customize the message shown to guest users
   - Supports basic HTML for formatting
   - Default: "Your cart contains items that require an account. Please log in or create an account to continue."

4. **Redirect Page**
   - Choose where to redirect guests
   - Options: My Account, Login Page
   - Default: My Account

### Example Configuration

```php
// Programmatically set options
update_option('gcbpt_settings', array(
    'enabled' => 'yes',
    'product_types' => array('downloadable', 'virtual'),
    'message' => 'Digital products require an account for access.',
    'redirect_page' => 'my-account'
));
```

## Usage

### Customer Experience

1. **Adding Products**: Customers can add any products to cart normally
2. **Checkout Process**: 
   - If cart contains restricted products, registration is required
   - A notice appears at the top of checkout
   - Guest checkout option is disabled
3. **Account Creation**: Customers must either log in or create an account

### Store Owner Workflow

1. Enable the plugin
2. Select product types that require accounts
3. Customize the notice message
4. Monitor customer registration rates

## Developer Documentation

### File Structure

```
guest-checkout-blocker-by-product-type/
├── guest-checkout-blocker-by-product-type.php  # Main plugin file
├── includes/
│   ├── class-gcbpt-main.php                    # Core functionality
│   ├── class-gcbpt-admin.php                   # Admin interface
│   ├── class-gcbpt-frontend.php                # Frontend functionality
│   ├── class-gcbpt-settings.php                # Settings management
│   └── class-gcbpt-activator.php               # Activation hooks
├── assets/
│   ├── css/
│   │   └── gcbpt-admin.css                     # Admin styles
│   └── js/
│       └── gcbpt-admin.js                      # Admin scripts
└── languages/
    └── guest-checkout-blocker-by-product-type.pot  # Translation template
```

### Main Classes

#### GCBPT_Main
Core plugin class that initializes all components.

```php
$plugin = new GCBPT_Main();
$plugin->run();
```

#### GCBPT_Admin
Handles admin interface and settings.

```php
// Hooks
add_action('admin_menu', array($admin, 'add_admin_menu'));
add_action('admin_init', array($admin, 'register_settings'));
```

#### GCBPT_Frontend
Manages frontend checkout modifications.

```php
// Key method
public function force_registration_for_product_types($registration_required)
```

## Hooks and Filters

### Filters

#### `gcbpt_available_product_types`
Modify available product types in settings.

```php
add_filter('gcbpt_available_product_types', function($types) {
    $types['custom_type'] = __('Custom Product Type', 'text-domain');
    return $types;
});
```

#### `woocommerce_checkout_registration_required`
Core WooCommerce filter used by the plugin.

### Actions

#### `gcbpt_before_settings_save`
Fired before settings are saved.

```php
add_action('gcbpt_before_settings_save', function($settings) {
    // Custom validation or processing
});
```

## Troubleshooting

### Common Issues

1. **Plugin doesn't appear in menu**
   - Ensure WooCommerce is active
   - Check user permissions (manage_woocommerce capability required)

2. **Settings not saving**
   - Verify nonce is present
   - Check for JavaScript errors
   - Ensure proper write permissions

3. **Guest checkout still available**
   - Confirm plugin is enabled
   - Verify product types are selected
   - Clear cache if using caching plugins

### Debug Mode

Enable WordPress debug mode to see detailed errors:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## Coding Standards

This plugin follows WordPress Coding Standards:

### PHP Standards

- PSR-4 autoloading structure
- Proper escaping with `esc_html()`, `esc_attr()`, etc.
- Nonce verification for forms
- Sanitization of all input data

### JavaScript Standards

- jQuery wrapper for compatibility
- Proper event delegation
- Localized strings for i18n

### CSS Standards

- BEM-like naming convention
- Mobile-responsive design
- Prefixed class names to avoid conflicts

### Security Best Practices

1. **Data Validation**
   ```php
   $product_types = array_map('sanitize_text_field', $_POST['gcbpt_product_types']);
   ```

2. **Nonce Verification**
   ```php
   wp_verify_nonce($_POST['gcbpt_settings_nonce'], 'gcbpt_save_settings')
   ```

3. **Capability Checks**
   ```php
   if (!current_user_can('manage_woocommerce')) {
       return;
   }
   ```

### Running PHPCS

```bash
# Install WordPress Coding Standards
composer require --dev dealerdirect/phpcodesniffer-composer-installer
composer require --dev wp-coding-standards/wpcs

# Run PHPCS
./vendor/bin/phpcs --standard=WordPress path/to/plugin/

# Fix automatically fixable issues
./vendor/bin/phpcbf --standard=WordPress path/to/plugin/
```

## Support

For support, feature requests, or bug reports:

1. Check the [FAQ section](#frequently-asked-questions)
2. Visit the WordPress.org support forum
3. Submit issues on GitHub
4. Contact support@example.com

## License

This plugin is licensed under GPL v2 or later. See LICENSE file for details.