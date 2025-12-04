<?php
/**
 * ------------------------------------------------------------------------------
 * Breakerino Checkout > Plugin
 * ------------------------------------------------------------------------------
 * @created     08/08/2025
 * @updated     08/08/2025
 * @version	    1.0.0
 * @author     	Breakerino
 * ------------------------------------------------------------------------------
 */

namespace Breakerino\Checkout;

defined('ABSPATH') || exit;

use Breakerino\Core\Abstracts\Plugin as PluginBase;

class Plugin extends PluginBase implements Constants {
	public const PLUGIN_ID         	= 'breakerino-checkout';
	public const PLUGIN_NAME		 		= 'Breakerino Checkout';
	public const PLUGIN_VERSION     = '1.0.0';

	public const PLUGIN_HOOKS = [
		'global' => [
			[
				'type'		=> 'filter',
				'hooks'		=> ['woocommerce_checkout_fields'],
				'callback' 	=> ['$this', 'handle_extend_checkout_fields'],
				'priority' 	=> 100,
				'args'		=> 1
			],
			[
				'type'		=> 'filter',
				'hooks'		=> ['wc_get_template'],
				'callback' 	=> ['$this', 'handle_register_templates'],
				'priority' 	=> 10,
				'args'		=> 5
			],
			[
				'type'		=> 'action',
				'hooks'		=> ['breakerino/assets-manager/assets_dirs'],
				'callback' 	=> ['$this', 'handle_register_assets_dir'],
				'priority' 	=> 10,
				'args'		=> 1
			],
			[
				'type'		=> 'action',
				'hooks'		=> ['breakerino/assets-manager/supported_asset_conditions'],
				'callback' 	=> ['$this', 'handle_adjust_supported_assets_conditions'],
				'priority' 	=> 20,
				'args'		=> 1
			],
			[
				'type'		=> 'action',
				'hooks'		=> ['woocommerce_checkout_order_review'],
				'callback' 	=> ['$this', 'handle_remove_order_review_payment'],
				'priority' 	=> 10,
				'args'		=> 1
			],
			[
				'type'		=> 'action',
				'hooks'		=> ['woocommerce_checkout_init'],
				'callback' 	=> ['$this', 'handle_checkout_init'],
				'priority' 	=> 10,
				'args'		=> 0
			],
			[
				'type'		=> 'action',
				'hooks'		=> ['woocommerce_before_checkout_form'],
				'callback' 	=> ['$this', 'handle_before_checkout_form'],
				'priority' 	=> 1,
				'args'		=> 0
			],
			[
				'type'		=> 'action',
				'hooks'		=> ['woocommerce_checkout_update_order_review'],
				'callback' 	=> ['$this', 'handle_checkout_before_update_order_review'],
				'priority' 	=> 10,
				'args'		=> 1
			],
			[
				'type'		=> 'filter',
				'hooks'		=> ['woocommerce_update_order_review_fragments'],
				'callback' 	=> ['$this', 'handle_update_order_review_fragments'],
				'priority' 	=> 10,
				'args'		=> 1
			],
			[
				'type'		=> 'action',
				'hooks'		=> ['woocommerce_before_checkout_process'],
				'callback' 	=> ['$this', 'handle_before_checkout_process'],
				'priority' 	=> 10,
				'args'		=> 0
			]
		]
	];
	
	public const PLUGIN_ASSETS = [
		'public' => [
			'script' => [],
			'style' => []
		]
	];
	
	/**
	 * Handle checkout fields
	 *
	 * @return void
	 */
	public function handle_extend_checkout_fields($checkoutFields) {
		$config = Helpers::get_checkout_config();
			
		foreach ($config['fields'] as &$fields) {
			$index = 0;
			foreach ($fields as &$field) {
				// Auto-assign priority
				$field['priority'] = $field['priority'] ?? (($index + 1) * 10);
				$index++;
			}
		}
		
		//$checkoutFields = array_merge($checkoutFields, $config['fields']);
		$checkoutFields = $config['fields'];
		
		return $checkoutFields;
	}
	
	/**
	 * Handle register checkout template
	 *
	 * @return void
	 */
	public function handle_register_templates($template, $templateName ) {
		switch ($templateName) {
			case 'checkout/form-checkout.php':
				return $this->get_file_path('views/checkout/index.php');
			case 'cart/cart.php':
				return $this->get_file_path('views/cart/index.php');
		}
		
		return $template;
	}
	
	/**
	 * Handle register assets dir
	 * 
	 * @return array 
	 */
	public function handle_register_assets_dir($assetsDir) {
		$assetsDir[] = dirname(__DIR__) . '/assets';
		return $assetsDir;
	}
	
	/**
	 * Handle register assets dir
	 * 
	 * @return array 
	 */
	public function handle_adjust_supported_assets_conditions($conditions) {
		$conditions = array_unique(
			array_merge($conditions, ['is_cart', 'is_checkout'])
		);
		
		return $conditions;
	}
	
	/**
	 * Handle remove order review
	 *
	 * @return void
	 */
	public function handle_remove_order_review_payment() {
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
	}
	
	/**
	 * Handle checkout init
	 *
	 * @return void
	 */
	public function handle_checkout_init() {
		if ( ! ( WC()->session instanceof \WC_Session ) ) {
			return;
		}
		
		if ( empty( WC()->session->get( 'selected_shipping_methods' ) ) ) {
			Helpers::reset_selected_shipping_method();
		}
		
		if ( empty( WC()->session->get( 'selected_payment_method' ) ) ) {
			Helpers::reset_selected_payment_method();
		}
	}
	
	/**
	 * Handle before checkout form
	 *
	 * @return void
	 */
	public function handle_before_checkout_form() {
		WC()->session->__unset( 'selected_shipping_methods' );
		WC()->session->__unset( 'selected_payment_method' );
		
		WC()->cart->set_shipping_total( 0 );
		WC()->cart->set_shipping_tax( 0 );
	}
	
	/**
	 * Handle checkout before update order review
	 *
	 * @return void
	 */
	public function handle_checkout_before_update_order_review($postData) {
		wp_parse_str( $postData, $postData );
		
		$selectedShippingMethods = isset($postData['methods']['shipping']) ? wc_clean( wp_unslash($postData['methods']['shipping'] ) ) : [];
		$selectedPaymentMethod = isset($postData['methods']['payment'] ) ? wc_clean( wp_unslash($postData['methods']['payment'] ) ) : null;

		$_POST['shipping_method'] = $selectedShippingMethods;
		WC()->session->set( 'chosen_shipping_methods', $selectedShippingMethods );
		WC()->session->set( 'selected_shipping_methods', $selectedShippingMethods );
		
		$_POST['payment_method'] = $selectedPaymentMethod;
		WC()->session->set( 'chosen_payment_method', $selectedPaymentMethod );
		WC()->session->set( 'selected_payment_method', $selectedPaymentMethod );
	}
	
	/**
	 * Handle update order review fragments
	 * 
	 * @return void
	 */
	public function handle_update_order_review_fragments($fragments) {
		// Unset default WooCommerce fragments		
		unset($fragments['.woocommerce-checkout-payment']);
		unset($fragments['.woocommerce-checkout-review-order-table']);
		
		// Add custom fragments
		$fragments['.brk-ecommerce-checkout-methods--shipping'] = Helpers::get_view_html('checkout/shipping');
		$fragments['.brk-ecommerce-checkout-methods--payment'] = Helpers::get_view_html('checkout/payment');
		$fragments['.brk-ecommerce-checkout-section--summary'] = Helpers::get_view_html('checkout/section', [
			'id' => 'summary',
			'type' => 'summary',
			'text' => __('Order summary', 'breakerino-checkout'),
			'content_view' => 'checkout/order-summary'
		]);
		
		return $fragments;
	}
	
	/**
	 * Handle before checkout process
	 *
	 * @return void
	 */
	public function handle_before_checkout_process() {
		$selectedShippingMethods = isset($_POST['methods']['shipping']) ? wc_clean( wp_unslash($_POST['methods']['shipping'] ) ) : [];
		$selectedPaymentMethod = isset($_POST['methods']['payment'] ) ? wc_clean( wp_unslash($_POST['methods']['payment'] ) ) : null;

		$_POST['shipping_method'] = $selectedShippingMethods;
		WC()->session->set( 'chosen_shipping_methods', $selectedShippingMethods );
		WC()->session->set( 'selected_shipping_methods', $selectedShippingMethods );
		
		$_POST['payment_method'] = $selectedPaymentMethod;
		WC()->session->set( 'chosen_payment_method', $selectedPaymentMethod );
		WC()->session->set( 'selected_payment_method', $selectedPaymentMethod );
	}
}
