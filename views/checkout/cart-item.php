<?php
defined('ABSPATH') || exit;
?>

<div class="brk-ecommerce-checkout-cart-item">
	<div class="brk-ecommerce-checkout-cart-item-info">
		<div class="brk-ecommerce-checkout-cart-item-image">
			<img src="<?= $image; ?>" alt="<?= $name ?>">
		</div>
		<div class="brk-ecommerce-checkout-cart-item-text">
			<a href="<?= $permalink ?>" target="_blank" class="brk-ecommerce-checkout-cart-item-name"><?= $name; ?></a>
			<span class="brk-ecommerce-checkout-cart-item-quantity"><?= sprintf(_n('%sx', '%sx', $quantity, 'breakerino-checkout'), $quantity); ?></span>
			<div class="brk-ecommerce-checkout-cart-item-prices brk-ecommerce-checkout-cart-item-prices--mobile">
				<span class="brk-ecommerce-checkout-cart-item-subtotal"><?= $subtotal; ?></span>
				<span class="brk-ecommerce-checkout-cart-item-price-per-unit"><?= sprintf(_n('%s/pc', '%s/pc', $quantity, 'breakerino-checkout'), $price); ?></span>
			</div>
		</div>
	</div>

	<div class="brk-ecommerce-checkout-cart-item-prices brk-ecommerce-checkout-cart-item-prices--desktop">
		<span class="brk-ecommerce-checkout-cart-item-subtotal"><?= $subtotal; ?></span>
		<span class="brk-ecommerce-checkout-cart-item-price-per-unit"><?= sprintf(_n('%s/pc', '%s/pc', $quantity, 'breakerino-checkout'), $price); ?></span>
	</div>
</div>