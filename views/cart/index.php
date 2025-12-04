<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$cartItems = Helpers::get_cart_items();
?>

<?php do_action('woocommerce_before_cart');  ?>

<div class="brk-ecommerce-cart">
	<div class="brk-ecommerce-cart__left">
		<form class="brk-ecommerce-cart-form woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
			<?php do_action('woocommerce_before_cart_table'); ?>
			<?php Helpers::get_view('cart/cart-table', ['items' => $cartItems]); ?>
			<?php do_action('woocommerce_after_cart_table'); ?>
		</form>
	</div>
	<div class="brk-ecommerce-cart__right">
		<?php do_action('woocommerce_cart_collaterals');  ?>
	</div>
</div>

<?php do_action('woocommerce_after_cart');  ?>