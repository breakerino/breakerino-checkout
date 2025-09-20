<?php

defined( 'ABSPATH' ) || exit;

use Breakerino\Checkout\Helpers;

$paymentMethods = Helpers::get_payment_methods();

if ( empty($paymentMethods) || ! is_array($paymentMethods) ) {
	return;
}
?>

<div class="brk-checkout-methods brk-checkout-methods--payment" data-brk-checkout-type="payment">
	<?php foreach ( $paymentMethods as $paymentMethod ): ?>
		<?php Helpers::get_view( 'payment-method', ['paymentMethod' => $paymentMethod, 'isSelected' => false] ); ?>
	<?php endforeach; ?>
</div>