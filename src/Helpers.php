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

		if (! file_exists($templatePath)) {
			if (\Breakerino\Core\Helpers::is_dev_mode()) {
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
			$fields = array_map(function ($field) {
				if (! is_array($field['class'])) {
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

		$shippingMethodName = null;

		if ($selectedShippingMethod) {
			$shippingMethods = WC()->shipping->get_packages()[0]['rates'] ?? [];

			if (isset($shippingMethods[$selectedShippingMethod])) {
				$shippingMethodName = $shippingMethods[$selectedShippingMethod]->get_label();
			} else {
				$shippingMethodName = __('Shipping', 'breakerino-checkout');
			}

			$fees = array_merge([
				$selectedShippingMethod => (object) [
					'id' => 'shipping',
					'name' => $shippingMethodName,
					'tax_class' => '', // TODO
					'taxable' => 1,
					'amount' => WC()->cart->get_shipping_total(),
					'total' => WC()->cart->get_shipping_total(),
					'tax_data' => [
						1 => WC()->cart->get_shipping_tax()
					],
					'tax' => WC()->cart->get_shipping_tax(),
				]
			], $fees);
		}

		return $fees;
	}

	public static function get_order_fees($order) {
		if (! $order || ! $order instanceof \WC_Order) {
			return [];
		}

		$fees = [];
		foreach ($order->get_fees() as $item) {
			// WC_Order_Item_Fee does not have is_taxable(), but we can infer by whether tax amount is non-zero, or tax_class set
			$is_taxable = false;
			$tax_class = $item->get_tax_class();
			$tax_total = $item->get_total_tax();
			if ($tax_total > 0 || (is_string($tax_class) && $tax_class !== '')) {
				$is_taxable = true;
			}
			$fees[] = (object) [
				'id'        => $item->get_id(),
				'name'      => $item->get_name(),
				'tax_class' => $tax_class,
				'taxable'   => $is_taxable,
				'amount'    => $item->get_total(),
				'total'     => $item->get_total(),
				'tax_data'  => $item->get_taxes()['total'],
				'tax'       => $tax_total,
			];
		}

		// Add shipping as a "fee" if selected shipping method exists.
		$shipping_methods = $order->get_shipping_methods();
		if (!empty($shipping_methods)) {
			foreach ($shipping_methods as $sm) {
				// WC_Order_Item_Shipping also does not have is_taxable; same logic
				$is_taxable = false;
				$tax_class = $sm->get_tax_class();
				$tax_total = $sm->get_total_tax();
				if ($tax_total > 0 || (is_string($tax_class) && $tax_class !== '')) {
					$is_taxable = true;
				}
				$fees = array_merge([(object) [
					'id'        => $sm->get_id(),
					'name'      => $sm->get_name(),
					'tax_class' => $tax_class,
					'taxable'   => $is_taxable,
					'amount'    => $sm->get_total(),
					'total'     => $sm->get_total(),
					'tax_data'  => $sm->get_taxes()['total'],
					'tax'       => $tax_total,
				]], $fees);
				// Only add first shipping method to fees to match cart logic
				break;
			}
		}

		return $fees;
	}

	public static function get_order_subtotal($order, $includingTax = true) {
		if (!$order || !($order instanceof \WC_Order)) {
			return 0;
		}

		$subtotal = (float) $order->get_subtotal();

		if ($includingTax) {
			$subtotalTax = 0;

			foreach ($order->get_items() as $item) {
				$subtotalTax += (float) $item->get_subtotal_tax();
			}

			return $subtotal + $subtotalTax;
		}

		return $subtotal;
	}

	/**
	 * Get formatted subtotal/total HTML for either cart or order.
	 *
	 * @param WC_Cart|WC_Order|null $source
	 * @param string $type 'subtotal' or 'total'
	 * @return string
	 */
	public static function get_order_total_html($source = null, $type = 'total') {
		if (null === $source) {
			$source = WC()->cart;
		}

		if (! $source || (! $source instanceof \WC_Cart && ! $source instanceof \WC_Order)) {
			return '';
		}

		$type = strtolower($type);
		$value = '';
		$html = '';

		switch ($type) {
			case 'subtotal':
				if ($source instanceof \WC_Order) {
					// No get_formatted_order_subtotal in WC_Order, so use subtotal directly and format
					$subtotal      = $source->get_subtotal();
					$subtotal_tax  = $source->get_subtotal_tax();
					if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_cart')) {
						$value = wc_price($subtotal + $subtotal_tax, ['currency' => $source->get_currency()]);
					} else {
						$value = wc_price($subtotal, ['currency' => $source->get_currency()]);
					}
				} else {
					// For cart
					if ($source->display_prices_including_tax() && wc_tax_enabled()) {
						$value = wc_price($source->get_subtotal() + $source->get_subtotal_tax());
					} else {
						$value = wc_price($source->get_subtotal());
					}
				}
				break;

			case 'total':
			default:
				if ($source instanceof \WC_Cart) {
					$value = $source->get_total();
				} else {
					$value = $source->get_formatted_order_total();
				}
				break;
		}

		if (empty($value)) {
			return '';
		}

		$html = sprintf(
			'<strong>%s</strong> ',
			$value
		);

		switch ($type) {
			case 'subtotal':
				// Show subtotal + (includes tax) note (where applicable)
				if (
					$source instanceof \WC_Cart &&
					wc_tax_enabled() &&
					$source->display_prices_including_tax()
				) {
					$taxStrings = [];
					$cartTaxTotals  = $source->get_tax_totals();

					if (get_option('woocommerce_tax_total_display') === 'itemized') {
						foreach ($cartTaxTotals as $tax) {
							$taxStrings[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
						}
					} elseif (! empty($cartTaxTotals)) {
						$taxStrings[] = sprintf(
							'%s %s',
							wc_price($source->get_subtotal_tax()),
							WC()->countries->tax_or_vat()
						);
					}

					if (! empty($taxStrings)) {
						$taxText = sprintf(
							__('(includes %s)', 'woocommerce'),
							implode(', ', $taxStrings)
						);

						$html .= sprintf(
							'<small>%s</small>',
							wp_kses_post($taxText)
						);
					}
				}
				return apply_filters('breakerino/checkout/order_total_html', $html, null, $source, $type);
			case 'total':
			default:
				// Proper support for WC_Order and WC_Cart for display including tax.
				// If not an object or relevant class, fallback
				if (!is_object($source) || (!($source instanceof \WC_Cart) && !($source instanceof \WC_Order))) {
					return apply_filters('breakerino/checkout/order_total_html', $html, null, $source, $type);
				}

				// Respect "Prices entered with tax" and "Display prices in the shop" settings
				$displayInclTax = false;
				if ($source instanceof \WC_Cart) {
					$displayInclTax = $source->display_prices_including_tax();
				} elseif ($source instanceof \WC_Order) {
					// Orders store tax_included_meta at time of purchase, use the filter for consistency
					// This also allows plugins to filter this on the order view
					$displayInclTax = apply_filters('woocommerce_order_amount_display_incl_tax', true, $source);
				}

				if (!wc_tax_enabled() || !$displayInclTax) {
					return apply_filters('breakerino/checkout/order_total_html', $html, null, $source, $type);
				}

				$taxStrings = [];
				$cartTaxTotals = $source->get_tax_totals();

				if (get_option('woocommerce_tax_total_display') === 'itemized') {
					foreach ($cartTaxTotals as $tax) {
						$taxStrings[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
					}
				} elseif (!empty($cartTaxTotals)) {
					if ($source instanceof \WC_Cart) {
						$totalTaxes = $source->get_taxes_total(true, true);
					} elseif ($source instanceof \WC_Order) {
						// true, true parameters: include shipping, include refunds
						$totalTaxes = $source->get_total_tax();
					} else {
						$totalTaxes = 0;
					}
					$taxStrings[] = sprintf(
						'%s %s',
						wc_price($totalTaxes),
						WC()->countries->tax_or_vat()
					);
				}

				if (empty($taxStrings)) {
					return apply_filters('breakerino/checkout/order_total_html', $html, null, $source, $type);
				}

				// Tax estimate notice logic (show "estimated for" only for cart, not orders)
				$showEstimated = false;
				$country = '';
				if ($source instanceof \WC_Cart) {
					$taxableAddress = WC()->customer->get_taxable_address();
					if (
						WC()->customer->is_customer_outside_base() &&
						!WC()->customer->has_calculated_shipping()
					) {
						$showEstimated = true;
						$country = sprintf(
							'%s%s',
							WC()->countries->estimated_for_prefix($taxableAddress[0]),
							WC()->countries->countries[$taxableAddress[0]]
						);
					}
				}

				if ($showEstimated) {
					/* translators: 1: tax amount 2: country name */
					$taxText = sprintf(
						__('(includes %1$s estimated for %2$s)', 'woocommerce'),
						implode(', ', $taxStrings),
						$country
					);
				} else {
					/* translators: %s: tax amount */
					$taxText = sprintf(
						__('(includes %s)', 'woocommerce'),
						implode(', ', $taxStrings)
					);
				}

				$html .= sprintf(
					'<small>%s</small>',
					wp_kses_post($taxText)
				);

				return apply_filters('breakerino/checkout/order_total_html', $html, ($source instanceof \WC_Order ? $source : null), $source, $type);
		}
	}
}
