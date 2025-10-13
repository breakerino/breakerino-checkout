<?php

defined( 'ABSPATH' ) || exit;

use Breakerino\Checkout\Helpers;

$billingFields = WC()->checkout->get_checkout_fields( 'billing' );
$shippingFields = WC()->checkout->get_checkout_fields( 'shipping' );
$companyFields = WC()->checkout->get_checkout_fields( 'company' );
$orderNoteFields = WC()->checkout->get_checkout_fields( 'order_note' );
?>

<div class="brk-checkout-form-field-group" data-brk-checkout-id="billing">
	<?php foreach ($billingFields as $key => $field): ?>
		<?php $field['custom_attributes']['data-brk-checkout-id'] = $key; ?>
		<?php if ($field['required']): $field['custom_attributes']['data-brk-checkout-required'] = 'true'; endif; ?>
		<?php woocommerce_form_field($key, $field, WC()->checkout->get_value($key)); ?>
	<?php endforeach; ?>
</div>

<?php Helpers::get_view('conditional-section', [
	'id' => 'shipping',
	'id' => 'ship-to-different-address',
	'input_id' => 'ship-to-different-address',
	'input_name' => 'ship_to_different_address',
	'active' => false,
	'text' => __('Ship to a different address', 'breakerino-checkout'),
	'fields' => $shippingFields
]); ?>

<?php Helpers::get_view('conditional-section', [
	'id' => 'company',
	'input_id' => 'purchase-for-company',
	'input_name' => 'purchase_for_company',
	'active' => false,
	'text' => __('Purchase for a company', 'breakerino-checkout'),
	'fields' => $companyFields
]); ?>

<?php
// Helpers::get_view('conditional-section', [
// 	'id' => 'account',
// 	'input_id' => 'create-an-account',
// 	'input_name' => 'createaccount',
// 	'active' => false,
// 	'text' => __('Create an account', 'breakerino-checkout')
// ]); 
?>

<?php Helpers::get_view('conditional-section', [
	'id' => 'order-note',
	'input_id' => 'add-order-note',
	'input_name' => 'add-order-note',
	'active' => false,
	'text' => __('Add order note', 'breakerino-checkout'),
	'fields' => $orderNoteFields
]); ?>