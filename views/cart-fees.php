<?php

defined('ABSPATH') || exit;

$items = is_array($items) ? $items : [];
?>

<?php if (! empty($items)) : ?>
	<div class="brk-checkout-fees">	
		<?php foreach ($items as $item) : ?>
			<div class="brk-checkout-fee-item">
				<span class="brk-checkout-fee-item-label"><?= $item->name; ?></span>
				<span class="brk-checkout-fee-item-value"><?= wc_price($item->amount + $item->tax); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>