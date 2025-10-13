<?php
/**
 * ------------------------------------------------------------------------------
 * Breakerino Checkout > Helpers
 * ------------------------------------------------------------------------------
 * @created     08/08/2025
 * @updated     08/08/2025
 * @version	    1.0.0
 * @author     	Breakerino
 * ------------------------------------------------------------------------------
 */

namespace Breakerino\Checkout;

defined('ABSPATH') || exit;

class Helpers {
	public static function get_view(string $templateName, array $args = []) {
		extract($args);
		
		$templatePath = sprintf('views/%s.php', $templateName);
		$templatePath = Plugin::instance()->get_file_path($templatePath);
		
		if ( ! file_exists($templatePath) ) {
			if ( \Breakerino\Core\Helpers::is_dev_mode() ) {
				echo sprintf('View "%s" not found.', $templatePath);
			}
			return;
		}
		
		include $templatePath;
	}
	
	public static function get_view_html(string $templateName, array $args = []) {
		ob_start();
		self::get_view($templateName, $args);
		return ob_get_clean();
	}
	
	public static function get_checkout_config() {
		$config = apply_filters('breakerino/checkout/config', ['fields' => []]);
		
		foreach ($config['fields'] as $type => &$fields) {
			$fields = array_map(function($field) {
				if ( ! is_array($field['class']) ) {
					$field['class'] = [];
				}
				
				$field['class'][] = 'brk-checkout-form-field';
				
				return $field;
			}, $fields);
		}
		
		return $config;
	}	
	
	public static function get_shipping_methods($index = 0) {
		return WC()->shipping->get_packages()[$index]['rates'] ?? null;
	}
	
	public static function get_payment_methods() {
		$methods = WC()->payment_gateways->get_available_payment_gateways();
		return $methods;
	}
	
	public static function get_selected_shipping_method($index = 0) {
		return WC()->session->get('selected_shipping_methods')[$index] ?? null;
	}
	
	public static function get_selected_payment_method() {
		return WC()->session->get('selected_payment_method') ?? null;
	}
	
	public static function reset_selected_shipping_method($index = 0) {
		$selectedShippingMethods = WC()->session->get('chosen_shipping_methods', []);
		
		if (! isset($selectedShippingMethods[$index])) {
			return;
		}
		
		unset($selectedShippingMethods[$index]);
		WC()->session->set('chosen_shipping_methods', $selectedShippingMethods);
	}

	public static function reset_selected_payment_method() {
		WC()->session->__unset('chosen_payment_method');
	}
	
	public static function get_after_shipping_rate_content($shippingMethod, $index) {
		ob_start();
		do_action('woocommerce_after_shipping_rate', $shippingMethod, $index);
		return ob_get_clean();
	}
	
	public static function get_cart_items() {
		return WC()->cart->get_cart();
	}
	
	public static function get_cart_fees() {
		$fees = WC()->cart->get_fees();
		
		$selectedShippingMethod = self::get_selected_shipping_method();
		
		if ( $selectedShippingMethod ) {
			$fees = array_merge([(object) [
				'id' => 'shipping',
				'name' => __('Shipping', 'breakerino-checkout'),
				'tax_class' => '',
				'taxable' => 1,
				'amount' => WC()->cart->get_shipping_total(),
				'total' => WC()->cart->get_shipping_total(),
				'tax_data' => [
					1 => WC()->cart->get_shipping_tax()
				],
				'tax' => WC()->cart->get_shipping_tax(),
			]], $fees);
		}
		
		return $fees;
	}
}
