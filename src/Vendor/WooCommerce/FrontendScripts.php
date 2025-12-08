<?php

namespace Breakerino\Checkout\Vendor\WooCommerce;

defined('ABSPATH') || exit;

use Automattic\Jetpack\Constants;

class FrontendScripts {
	/**
	 * Localize a WC script once.
	 *
	 * @param string $handle Script handle the data will be attached to.
	 */
	public static function localize_script( $handle ) {
		$data = self::get_script_data( $handle );

		if ( ! $data ) {
			return;
		}

		$name = str_replace( '-', '_', $handle ) . '_params';
		wp_localize_script( 'vendor-' . $handle, $name, apply_filters( $name, $data ) );
	}

	/**
	 * Return data for script handles.
	 *
	 * @param  string $handle Script handle the data will be attached to.
	 * @return array|bool
	 */
	public static function get_script_data( $handle ) {
		global $wp;

		switch ( $handle ) {
			case 'wc-checkout':
				$params = array(
					'ajax_url'                  => WC()->ajax_url(),
					'wc_ajax_url'               => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'update_order_review_nonce' => wp_create_nonce( 'update-order-review' ),
					'apply_coupon_nonce'        => wp_create_nonce( 'apply-coupon' ),
					'remove_coupon_nonce'       => wp_create_nonce( 'remove-coupon' ),
					'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
					'checkout_url'              => \WC_AJAX::get_endpoint( 'checkout' ),
					'is_checkout'               => is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,
					'debug_mode'                => Constants::is_true( 'WP_DEBUG' ),
					/* translators: %s: Order history URL on My Account section */
					'i18n_checkout_error'       => sprintf( esc_attr__( 'There was an error processing your order. Please check for any charges in your payment method and review your <a href="%s">order history</a> before placing the order again.', 'woocommerce' ), esc_url( wc_get_account_endpoint_url( 'orders' ) ) ),
				);
				break;
			case 'wc-cart':
				$params = array(
					'ajax_url'                     => WC()->ajax_url(),
					'wc_ajax_url'                  => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'update_shipping_method_nonce' => wp_create_nonce( 'update-shipping-method' ),
					'apply_coupon_nonce'           => wp_create_nonce( 'apply-coupon' ),
					'remove_coupon_nonce'          => wp_create_nonce( 'remove-coupon' ),
				);
				break;
			case 'wc-cart-fragments':
				$params = array(
					'ajax_url'        => WC()->ajax_url(),
					'wc_ajax_url'     => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'cart_hash_key'   => apply_filters( 'woocommerce_cart_hash_key', 'wc_cart_hash_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
					'fragment_name'   => apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
					'request_timeout' => 5000,
				);
				break;
			default:
				$params = false;
		}

		return apply_filters( 'woocommerce_get_script_data', $params, $handle );
	}
}