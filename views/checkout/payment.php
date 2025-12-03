<?php

defined( 'ABSPATH' ) || exit;

use Breakerino\Checkout\Helpers;

$paymentMethods = Helpers::get_payment_methods();

if ( empty($paymentMethods) || ! is_array($paymentMethods) ) {
	return;
}
?>

<div class="brk-ecommerce-checkout-methods brk-ecommerce-checkout-methods--payment" data-brk-ecommerce-checkout-type="payment">
	<?php foreach ( $paymentMethods as $paymentMethod ): ?>
		<?php Helpers::get_view( 'checkout/payment-method', ['paymentMethod' => $paymentMethod, 'isSelected' => false] ); ?>
	<?php endforeach; ?>
</div>