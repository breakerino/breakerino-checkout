<?php

defined('ABSPATH') || exit;

?>

<div class="brk-checkout-total">
	<span class="brk-checkout-total-label"><?php _e('Total', 'breakerino-checkout'); ?></span>
	<span class="brk-checkout-total-value"><?= wc_cart_totals_order_total_html(); ?></span>
</div>