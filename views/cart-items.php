<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$items = is_array($items) ? $items : [];
?>

<?php if (! empty($items)): ?>
	<div class="brk-checkout-cart-items">
		<?php do_action('woocommerce_review_order_before_cart_contents'); ?>

		<?php foreach ($items as $itemKey => $item) :
			$product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $itemKey);

			if (! $product || ! $product->exists() || $item['quantity'] <= 0 || ! apply_filters('woocommerce_checkout_cart_item_visible', true, $item, $itemKey)) {
				continue;
			}

			$image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');

			$name = apply_filters('woocommerce_cart_item_name', $product->get_name(), $item, $itemKey);
			$quantity = apply_filters('woocommerce_cart_item_quantity', $item['quantity'], $item, $itemKey);
			$subtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($product, $item['quantity']), $item, $itemKey);
			$pricePerUnit =  WC()->cart->get_product_subtotal($product, 1);

			// TODO: Cart item template
		?>
			<div class="brk-checkout-cart-item">
				<div class="brk-checkout-cart-item-info">
					<div class="brk-checkout-cart-item-image">
						<img src="<?= $image; ?>" alt="<?= $product->get_name(); ?>">
						<span class="brk-checkout-cart-item-quantity"><?= sprintf(_n('%sx', '%sx', $quantity, 'breakerino-checkout'), $quantity); ?></span>
					</div>
					<div class="brk-checkout-cart-item-text">
						<a href="<?= $product->get_permalink(); ?>" target="_blank" class="brk-checkout-cart-item-name"><?= $name; ?></a>
					</div>
				</div>

				<div class="brk-checkout-cart-item-prices">
					<span class="brk-checkout-cart-item-subtotal"><?= $subtotal; ?></span>
					<span class="brk-checkout-cart-item-price-per-unit"><?= sprintf(_n('%s/pc', '%s/pc', $quantity, 'breakerino-checkout'), $pricePerUnit); ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>