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
				
				$field['class'][] = 'brk-ecommerce-checkout-form-field';
				
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

	/**
	 * Get formatted order total HTML for either cart or order.
	 *
	 * @param WC_Cart|WC_Order|null $source
	 * @return string
	 */
	public static function get_order_total_html( $source = null ) {
		if ( null === $source ) {
			$source = WC()->cart;
		}

		if ( ! $source || ( ! $source instanceof \WC_Cart && ! $source instanceof \WC_Order ) ) {
			return '';
		}

		$orderTotal = $source instanceof \WC_Cart
			? $source->get_total()
			: $source->get_formatted_order_total();

		if ( empty( $orderTotal ) ) {
			return '';
		}

		$html = sprintf(
			'<strong>%s</strong> ',
			$orderTotal
		);

		if ( $source instanceof \WC_Order ) {
			return apply_filters( 'breakerino/checkout/order_total_html', $html, $source, null );
		}

		if ( ! wc_tax_enabled() || ! $source->display_prices_including_tax() ) {
			return apply_filters( 'breakerino/checkout/order_total_html', $html, null, $source );
		}

		$taxStrings = [];
		$cartTaxTotals  = $source->get_tax_totals();

		if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) {
			foreach ( $cartTaxTotals as $code => $tax ) {
				$taxStrings[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
			}
		} elseif ( ! empty( $cartTaxTotals ) ) {
			$taxStrings[] = sprintf(
				'%s %s',
				wc_price( $source->get_taxes_total( true, true ) ),
				WC()->countries->tax_or_vat()
			);
		}

		if ( empty( $taxStrings ) ) {
			return apply_filters( 'breakerino/checkout/order_total_html', $html, null, $source );
		}

		$taxableAddress = WC()->customer->get_taxable_address();

		if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
			$country = sprintf(
				'%s%s',
				WC()->countries->estimated_for_prefix( $taxableAddress[0] ),
				WC()->countries->countries[ $taxableAddress[0] ]
			);

			/* translators: 1: tax amount 2: country name */
			$taxText = sprintf(
				__( '(includes %1$s estimated for %2$s)', 'breakerino-checkout' ),
				implode( ', ', $taxStrings ),
				$country
			);
		} else {
			/* translators: %s: tax amount */
			$taxText = sprintf(
				__( '(includes %s)', 'breakerino-checkout' ),
				implode( ', ', $taxStrings )
			);
		}

		$html .= sprintf(
			'<small>%s</small>',
			wp_kses_post( $taxText )
		);

		return apply_filters( 'breakerino/checkout/order_total_html', $html, null, $source );
	}
}
