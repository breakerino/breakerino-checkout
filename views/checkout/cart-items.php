<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$items = is_array($items) ? $items : [];
?>

<?php if (! empty($items)): ?>	
	<div class="brk-ecommerce-checkout-cart-items">
		<?php foreach ($items as $itemKey => $item) :
			$product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $itemKey);

			if (! $product || ! $product->exists() || $item['quantity'] <= 0 || ! apply_filters('woocommerce_checkout_cart_item_visible', true, $item, $itemKey)) {
				continue;
			}

			$image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
			$permalink = $product->get_permalink();
			$name = apply_filters('woocommerce_cart_item_name', $product->get_name(), $item, $itemKey);
			$quantity = apply_filters('woocommerce_cart_item_quantity', $item['quantity'], $item, $itemKey);
			$subtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($product, $item['quantity']), $item, $itemKey);
			$price =  WC()->cart->get_product_subtotal($product, 1);
		?>
			<?php Helpers::get_view(
				'checkout/cart-item',
				compact('itemKey', 'product', 'name', 'image', 'quantity', 'price', 'subtotal', 'permalink')
				); ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>