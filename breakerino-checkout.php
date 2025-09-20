<?php
/**
 * Plugin Name: Breakerino Checkout
 * Plugin URI:  https://breakerino.me
 * Description: Step by step checkout for WooCommerce. Provides a streamlined, multi-step checkout process to improve user experience and increase conversions.
 * Version:     1.0.0
 * Author:      Breakerino
 * Author URI:  https://breakerino.me
 * Text Domain: breakerino-checkout
 * Domain Path: /languages
 * Requires at least: 6.7
 * Requires PHP: 8.1
 *
 * @package   Breakerino
 * @author    Breakerino
 * @link      https://www.breakerino.me
 * @copyright 2025 Breakerino
 */

defined( 'ABSPATH' ) || exit;

define( 'BREAKERINO_CHECKOUT_PLUGIN_FILE', __FILE__ );
define( 'BREAKERINO_CHECKOUT_VERSION', '1.0.0' );
define( 'BREAKERINO_CHECKOUT_DEPENDENCIES', ['woocommerce/woocommerce.php'] );

// Include autoloader
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * Returns the main plugin instance
 *
 * @since  1.0.0
 * @return Breakerino\Checkout\Plugin
 */
function BreakerinoCheckout() {
	return Breakerino\Checkout\Plugin::instance();
}

// Initialize plugin
add_action('breakerino_core_init', 'BreakerinoCheckout');