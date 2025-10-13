<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$type = ! empty($type) ? $type : 'fields';
$collapsed = is_bool($collapsed) ? $collapsed : false;
$classes = is_array($classes) ? $classes : [];
$content = ! empty($content) ? $content : null;
$contentView = ! empty($content_view) ? $content_view : null;
$contentHook = ! empty($content_hook) ? $content_hook : null;
?>

<div 
	class="brk-checkout-section<?= ' brk-checkout-section--'. $id; ?><?= $collapsed ? ' brk-checkout-section--collapsed' : ''; ?><?= ! empty($classes) ? ' ' . implode(' ', $classes) : ''; ?>" 
	data-brk-checkout-id="<?= $id; ?>" 
	data-brk-checkout-type="<?= $type; ?>"
>
	<div class="brk-checkout-section-header">
		<h2 class="brk-checkout-section-heading">
			<?php if (isset($index)): ?>
				<strong class="brk-checkout-section-heading__number"><?= $index; ?>.</strong>
			<?php endif; ?>
			<?php if (isset($text)): ?>
				<span class="brk-checkout-section-heading__text"><?= $text ?></span>
			<?php endif; ?>
		</h2>
	</div>
	
	<?php if ( ! empty($content) || ! empty($contentView) || ! empty($contentHook) ): ?>
		<div class="brk-checkout-section-content">
			<?= $content && $content ?>
			<?= $contentView && Helpers::get_view($contentView) ?>
			<?= $contentHook && do_action($contentHook) ?>
		</div>
	<?php endif; ?>
</div>
