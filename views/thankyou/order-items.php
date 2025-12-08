<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$items = is_array($items) ? $items : [];
?>

<?php if (! empty($items)): ?>	
	<div class="brk-ecommerce-checkout-cart-items">
		<?php foreach ($items as $itemKey => $item) :
			$product = $item->get_product();

			if (! $product || ! $product->exists() || $item['quantity'] <= 0 ) {
				continue;
			}

			$image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
			$permalink = $product->get_permalink();
			$name = $product->get_name();
			$quantity = $item->get_quantity();
			$price = round(($item->get_total() + $item->get_total_tax()) / ($item->get_quantity() > 0 ? $item->get_quantity() : 1));
			$subtotal = round($item->get_subtotal() + $item->get_subtotal_tax());
			
			$price = wc_price($price);
			$subtotal = wc_price($subtotal);
		?>
			<?php Helpers::get_view(
				'checkout/cart-item',
				compact('itemKey', 'product', 'name', 'image', 'quantity', 'price', 'subtotal', 'permalink')
				); ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>