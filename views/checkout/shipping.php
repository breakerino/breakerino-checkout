<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$index = 0;
$shippingMethods = Helpers::get_shipping_methods($index);

if (empty($shippingMethods) || ! is_array($shippingMethods)) {
	return;
}
?>

<div class="brk-ecommerce-checkout-methods brk-ecommerce-checkout-methods--shipping" data-brk-ecommerce-checkout-type="shipping">
	<?php foreach ($shippingMethods as $shippingMethod): ?>
		<?php Helpers::get_view('checkout/shipping-method', ['shippingMethod' => $shippingMethod, 'index' => $index, 'isSelected' => false]); ?>
	<?php endforeach; ?>
</div>