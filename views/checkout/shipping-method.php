<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$selectedMethod = Helpers::get_selected_shipping_method($index);
$afterContent = Helpers::get_after_shipping_rate_content($shippingMethod, $index);
?>
<label class="brk-ecommerce-checkout-method brk-ecommerce-checkout-method--shipping<?= $shippingMethod->id === $selectedMethod ? ' brk-ecommerce-checkout-method--selected' : ''; ?>" data-brk-ecommerce-checkout-id="<?= esc_attr($shippingMethod->id); ?>" for="shipping_method_<?= $index; ?>_<?= esc_attr(sanitize_title($shippingMethod->id)); ?>">
	<div class="brk-ecommerce-checkout-method__inner">
		<div class="brk-ecommerce-checkout-method__input">
			<input type="radio" name="methods[shipping][<?= $index; ?>]" data-index="<?= $index; ?>" id="shipping_method_<?= $index; ?>_<?= esc_attr(sanitize_title($shippingMethod->id)); ?>" value="<?= esc_attr($shippingMethod->id); ?>" class="shipping_method" <?php checked($shippingMethod->id, $selectedMethod); ?> />
			<span></span>
		</div>

		<div class="brk-ecommerce-checkout-method__content">
			<span class="brk-ecommerce-checkout-method__label"><?= esc_html($shippingMethod->get_label()); ?></span>
		</div>

		<div class="brk-ecommerce-checkout-method__price">
			<?= !empty($shippingMethod->get_cost()) ? wc_price($shippingMethod->get_cost()) : sprintf('<span>%s</span>', __('Free', 'woocommerce')); ?>
		</div>
	</div>
	<?php if (! empty($afterContent)): ?>
		<div class="brk-ecommerce-checkout-method__after">
			<?= $afterContent; ?>
		</div>
	<?php endif; ?>
</label>