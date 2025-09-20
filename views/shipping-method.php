<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$selectedMethod = Helpers::get_selected_shipping_method($index);
$afterContent = Helpers::get_after_shipping_rate_content($shippingMethod, $index);
?>
<label class="brk-checkout-method brk-checkout-method--shipping<?= $shippingMethod->id === $selectedMethod ? ' brk-checkout-method--selected' : ''; ?>" data-brk-checkout-id="<?= esc_attr($shippingMethod->id); ?>" for="shipping_method_<?= $index; ?>_<?= esc_attr(sanitize_title($shippingMethod->id)); ?>">
	<div class="brk-checkout-method__inner">
		<div class="brk-checkout-method__input">
			<input type="radio" name="methods[shipping][<?= $index; ?>]" data-index="<?= $index; ?>" id="shipping_method_<?= $index; ?>_<?= esc_attr(sanitize_title($shippingMethod->id)); ?>" value="<?= esc_attr($shippingMethod->id); ?>" class="shipping_method" <?php checked($shippingMethod->id, $selectedMethod); ?> />
			<span></span>
		</div>

		<div class="brk-checkout-method__content">
			<span class="brk-checkout-method__label"><?= esc_html($shippingMethod->get_label()); ?></span>
		</div>

		<div class="brk-checkout-method__price">
			<?= wc_price($shippingMethod->get_cost()); ?>
		</div>
	</div>
	<?php if (! empty($afterContent)): ?>
		<div class="brk-checkout-method__after">
			<?= $afterContent; ?>
		</div>
	<?php endif; ?>
</label>