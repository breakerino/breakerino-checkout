<?php

defined('ABSPATH') || exit;

?>

<div class="brk-checkout-subtotal">
	<span class="brk-checkout-subtotal-label"><?php _e('Subtotal', 'breakerino-checkout'); ?></span>
	<span class="brk-checkout-subtotal-value"><?= wc_cart_totals_subtotal_html(); ?></span>
</div>