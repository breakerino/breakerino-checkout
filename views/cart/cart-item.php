<?php
defined('ABSPATH') || exit;

if ($product->is_sold_individually()) {
	$minQuantity = 1;
	$maxQuantity = 1;
} else {
	$minQuantity = 0;
	$maxQuantity = $product->get_max_purchase_quantity();
}
?>

<tr class="brk-ecommerce-cart-item">
	<td class="brk-ecommerce-cart-item-info">
		<div class="brk-ecommerce-cart-item-image">
			<img src="<?= $image; ?>" alt="<?= $name ?>">
		</div>
		<a href="<?= $product->get_permalink(); ?>" target="_blank" class="brk-ecommerce-cart-item-name"><?= $name; ?></a>
	</td>
	<td class="brk-ecommerce-cart-item-price">
		<?= $price; ?>
	</td>
	<td class="brk-ecommerce-cart-item-quantity">
		<?= woocommerce_quantity_input(
			[
				'input_name'   		=> "cart[{$itemKey}][qty]",
				'input_value'  		=> $quantity,
				'max_value'    		=> $maxQuantity,
				'min_value'    		=> $minQuantity,
				'product_name' 		=> $name,
			],
			$product,
			false
		); ?>
	</td>
	<td class="brk-ecommerce-cart-item-subtotal">
		<?= $subtotal; ?>
	</td>
	<td class="brk-ecommerce-cart-item-remove">
		<a role="button"
			href="<?= esc_url(wc_get_cart_remove_url($itemKey)); ?>"
			class="brk-ecommerce-cart-item-remove__button remove"
			aria-label="<?= esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($name))); ?>"
			data-product_id="<?= esc_attr($product->get_id()); ?>"
			data-product_sku="<?= esc_attr($product->get_sku()); ?>">
			<?= apply_filters(
				'breakerino/ecommerce/cart/item/remove_icon',
				'<span>&times;</span>',
			); ?>
		</a>
	</td>
</tr>