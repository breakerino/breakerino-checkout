<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$selectedMethod = Helpers::get_selected_payment_method();
$paymentMethod->cost = apply_filters(sprintf('breakerino/checkout/payment_method/%s/cost', $paymentMethod->id), 0);
?>

<label data-brk-checkout-id="<?= esc_attr($paymentMethod->id); ?>" class="brk-checkout-method brk-checkout-method--payment<?= $selectedMethod === $paymentMethod->id ? ' brk-checkout-method--selected' : ''; ?>"  for="payment_method_<?= esc_attr($paymentMethod->id); ?>">
	<div class="brk-checkout-method__inner">
		<div class="brk-checkout-method__input">
			<input type="radio" name="methods[payment]" id="payment_method_<?= esc_attr($paymentMethod->id); ?>" value="<?= esc_attr($paymentMethod->id); ?>" <?php checked($selectedMethod, $paymentMethod->id); ?> />
			<span></span>
		</div>

		<div class="brk-checkout-method__content">
			<span class="brk-checkout-method__label"><?= esc_html($paymentMethod->get_title()); ?></span>
		</div>

		<div class="brk-checkout-method__price">
			<?= wc_price($paymentMethod->cost); ?>
		</div>
	</div>
	<?php if ($paymentMethod->has_fields() || $paymentMethod->get_description()) : ?>
		<div class="brk-checkout-method__after payment_box payment_method_<?php echo esc_attr($paymentMethod->id); ?>" <?php if (! $paymentMethod->chosen) : ?>style="display:none;" <?php endif; ?>>
			<?php $paymentMethod->payment_fields(); ?>
		</div>
	<?php endif; ?>
</label>