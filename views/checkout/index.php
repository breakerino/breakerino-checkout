<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$formClasses  = ['brk-ecommerce-checkout-form', 'woocommerce-checkout', 'checkout'];
?>

<?php do_action('woocommerce_before_checkout_form', $checkout); ?>

<form name="checkout" method="post" action="<?= esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" class="brk-ecommerce-checkout-form woocommerce-checkout checkout" data-brk-ecommerce-checkout-form  aria-label="<?= esc_attr__('Checkout', 'woocommerce'); ?>">
	<div class="brk-ecommerce-checkout">
		<div class="brk-ecommerce-checkout-loader brk-ecommerce-checkout-loader--visible">
			<span class="brk-ecommerce-checkout-loader__spinner"></span>
		</div>

		<div class="brk-ecommerce-checkout__left">
			<?php Helpers::get_view('checkout/section', [
				'id' => 'billing',
				'index' => 1,
				'text' => __('Billing details', 'breakerino-checkout'),
				'collapsed' => false,
				'content_view' => 'checkout/billing'
			]); ?>

			<?php Helpers::get_view('checkout/section', [
				'id' => 'shipping',
				'type' => 'methods',
				'index' => 2,
				'text' => __('Shipping', 'breakerino-checkout'),
				'collapsed' => true,
				'content_view' => 'checkout/shipping'
			]); ?>

			<?php Helpers::get_view('checkout/section', [
				'id' => 'payment',
				'type' => 'methods',
				'index' => 3,
				'text' => __('Payment', 'breakerino-checkout'),
				'collapsed' => true,
				'content_view' => 'checkout/payment'
			]); ?>
		</div>

		<div class="brk-ecommerce-checkout__right">
			<?php Helpers::get_view('checkout/section', [
				'id' => 'summary',
				'type' => 'summary',
				'text' => __('Order summary', 'breakerino-checkout'),
				'content_view' => 'checkout/order-summary'
			]); ?>
		</div>
	</div>
</form>