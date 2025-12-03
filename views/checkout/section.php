<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$type = ! empty($type) ? $type : 'fields';
$collapsed = is_bool($collapsed) ? $collapsed : false;
$classes = is_array($classes) ? $classes : [];
$content = ! empty($content) ? $content : null;
$contentView = ! empty($content_view) ? $content_view : null;
$contentHook = ! empty($content_hook) ? $content_hook : null;

$animationState = defined('DOING_AJAX') && DOING_AJAX ? 'finished' : 'pending';
?>

<div 
	class="brk-ecommerce-checkout-section<?= ' brk-ecommerce-checkout-section--'. $id; ?><?= $collapsed ? ' brk-ecommerce-checkout-section--collapsed' : ''; ?><?= ! empty($classes) ? ' ' . implode(' ', $classes) : ''; ?>" 
	data-brk-ecommerce-checkout-id="<?= $id; ?>" 
	data-brk-ecommerce-checkout-type="<?= $type; ?>"
	<?php // data-brk-animation="slide-from-bottom" ?>
	<?php // data-brk-animation-state=<?= $animationState; ?>
>
	<div class="brk-ecommerce-checkout-section-header">
		<h2 class="brk-ecommerce-checkout-section-heading">
			<?php if (isset($index)): ?>
				<strong class="brk-ecommerce-checkout-section-heading__number"><?= $index; ?>.</strong>
			<?php endif; ?>
			<?php if (isset($text)): ?>
				<span class="brk-ecommerce-checkout-section-heading__text"><?= $text ?></span>
			<?php endif; ?>
		</h2>
	</div>
	
	<?php if ( ! empty($content) || ! empty($contentView) || ! empty($contentHook) ): ?>
		<div class="brk-ecommerce-checkout-section-content">
			<?= $content && $content ?>
			<?= $contentView && Helpers::get_view($contentView) ?>
			<?= $contentHook && do_action($contentHook) ?>
		</div>
	<?php endif; ?>
</div>
