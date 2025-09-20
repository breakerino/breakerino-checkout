<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$index = 0;
$shippingMethods = Helpers::get_shipping_methods($index);

if (empty($shippingMethods) || ! is_array($shippingMethods)) {
	return;
}
?>

<div class="brk-checkout-methods brk-checkout-methods--shipping" data-brk-checkout-type="shipping">
	<?php foreach ($shippingMethods as $shippingMethod): ?>
		<?php Helpers::get_view('shipping-method', ['shippingMethod' => $shippingMethod, 'index' => $index, 'isSelected' => false]); ?>
	<?php endforeach; ?>
</div>