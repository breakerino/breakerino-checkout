<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$classes = is_array($classes) ? $classes : [];
$active = is_bool($active) ? $active : false;
$fields = is_array($fields) ? $fields : [];

$inputID = $input_id;
$inputName = $input_name;
?>

<div class="brk-checkout-conditional-section<?= ! empty($classes) ? ' ' . implode(' ', $classes) : '' ?><?= $active ? ' brk-checkout-conditional-section--active' : '' ?>" data-brk-checkout-id="<?= $id ?>">
	<label class="brk-checkout-conditional-section__header" for="<?= $inputID ?>">
		<input class="brk-checkout-conditional-section__header-input" type="checkbox" id="<?= $inputID ?>" name="<?= $inputName ?>" <?php checked(WC()->checkout->get_value($inputName) ?? $active, 1); ?> />
		<span class="brk-checkout-conditional-section__header-text"><?= $text; ?></span>
	</label>
	<?php if ( ! empty($content) || ! empty($fields) ): ?>
	<div class="brk-checkout-conditional-section__content">
		<?= $content ?>
		<?php if ( ! empty($fields) ): ?>
			<div class="brk-checkout-form-field-group" data-brk-checkout-id="<?= $id; ?>">
				<?php foreach ($fields as $key => $field): ?>
					<?php $field['custom_attributes']['data-brk-checkout-id'] = $key; ?>
					<?php if ($field['required']): $field['custom_attributes']['data-brk-checkout-required'] = 'true'; endif; ?>
					<?php woocommerce_form_field($key, $field, WC()->checkout->get_value($key)); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>