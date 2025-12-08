<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$proceedToCheckoutText = apply_filters(
	'breakerino/ecommerce/cart/proceed_to_checkout_button/text',
	__('Proceed to checkout', 'woocommerce')
);

$proceedToCheckoutIcon = apply_filters(
	'breakerino/ecommerce/cart/proceed_to_checkout_button/icon',
	null,
);
?>

<?php do_action('woocommerce_before_cart');  ?>

<div class="brk-ecommerce-loader">
	<span class="brk-ecommerce-loader__spinner"></span>
</div>

<div class="brk-ecommerce-cart">
	<div class="brk-ecommerce-cart__left">
		<form class="brk-ecommerce-cart-form woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
			<?php do_action('woocommerce_before_cart_table'); ?>

			<?php Helpers::get_view('cart/cart-table', [
				'items' => Helpers::get_cart_items()
			]); ?>

			<?php do_action('woocommerce_after_cart_table'); ?>

			<input type="hidden" name="update_cart" value="Update cart" />
			<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
		</form>
	</div>
	<div class="brk-ecommerce-cart__right">
		<?php Helpers::get_view('cart/cart-subtotal', [
			'subtotal' => Helpers::get_order_total_html(WC()->cart, 'subtotal')
		]); ?>

		<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="mt-button mt-button--lg mt-button--full mt-button--filled">
			<?php if ($proceedToCheckoutText): ?>
				<span class="mt-button__text"><?= esc_html($proceedToCheckoutText) ?></span>
			<?php endif; ?>

			<?php if ($proceedToCheckoutIcon): ?>
				<?= $proceedToCheckoutIcon; ?>
			<?php endif; ?>
		</a>
	</div>
</div>

<?php do_action('woocommerce_after_cart');  ?>