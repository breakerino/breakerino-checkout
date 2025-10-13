<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$formClasses  = ['woocommerce-checkout', 'checkout'];
?>

<div class="brxe-container before-checkout">
	<?php do_action('woocommerce_before_checkout_form', $checkout); ?>
</div>

<div class="brxe-container">
	<form name="checkout" method="post" class="<?= implode(' ', $formClasses); ?>" action="<?= esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" aria-label="<?= esc_attr__('Checkout', 'woocommerce'); ?>">
		<div class="brk-checkout">
			<div class="brk-checkout-loader brk-checkout-loader--visible">
				<span class="brk-checkout-loader__spinner"></span>
			</div>	
		
			<div class="brk-checkout__left">
				<?php Helpers::get_view('section', [
					'id' => 'billing',
					'index' => 1,
					'text' => __('Billing details', 'breakerino-checkout'),
					'collapsed' => false,
					'content_view' => 'billing'
				]); ?>
				
				<?php Helpers::get_view('section', [
					'id' => 'shipping',
					'type' => 'methods',
					'index' => 2,
					'text' => __('Shipping', 'breakerino-checkout'),
					'collapsed' => true,
					'content_view' => 'shipping'
				]); ?>
				
				<?php Helpers::get_view('section', [
					'id' => 'payment',
					'type' => 'methods',
					'index' => 3,
					'text' => __('Payment', 'breakerino-checkout'),
					'collapsed' => true,
					'content_view' => 'payment'
				]); ?>
			</div>
			
			<div class="brk-checkout__right">
				<?php Helpers::get_view('section', [
					'id' => 'summary',
					'type' => 'summary',
					'text' => __('Order summary', 'breakerino-checkout'),
					'content_view' => 'order-summary'
				]); ?>
			</div>
		</div>
	</form>
</div>