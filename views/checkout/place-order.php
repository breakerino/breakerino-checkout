<?php
defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$placeOrderText = apply_filters(
	'breakerino/checkout/place_order_button/text',
	apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce'))
);

$placeOrderIcon = apply_filters(
	'breakerino/checkout/place_order_button/icon',
	null,
);
?>

<div class="brk-ecommerce-checkout-place-order">
	<?php Helpers::get_view('checkout/terms'); ?>

	<?php do_action('woocommerce_review_order_before_submit'); ?>

	<button
		type="submit"
		class="brk-ecommerce-checkout-place-order-button mt-button mt-button--lg mt-button--filled mt-button--full"
		name="woocommerce_checkout_place_order"
		id="place_order"
		value="<?= esc_attr($placeOrderText) ?>"
		data-value="<?= esc_attr($placeOrderText) ?>"
		disabled>
		<?php if ($placeOrderText): ?>
			<span class="brk-ecommerce-checkout-place-order-button__text mt-button__text"><?= esc_html($placeOrderText) ?></span>
		<?php endif; ?>
		
		<?php if ($placeOrderIcon): ?>
			<?= $placeOrderIcon; ?>
		<?php endif; ?>
	</button>

	<?php do_action('woocommerce_review_order_after_submit'); ?>

	<?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
</div>