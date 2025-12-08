<?php
defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$orderItems = $order->get_items();
$orderSubtotal = Helpers::get_order_subtotal($order);

$completionNoticeIcon = apply_filters(
	'breakerino/ecommerce/thankyou/completion_notice/icon',
	null,
);

$orderNumberIcon = apply_filters(
	'breakerino/ecommerce/thankyou/order_number/icon',
	null,
);

$orderDateIcon = apply_filters(
	'breakerino/ecommerce/thankyou/order_date/icon',
	null,
);

$shippingMethodIcon = apply_filters(
	'breakerino/ecommerce/thankyou/shipping_method/icon',
	null,
);

$paymentMethodIcon = apply_filters(
	'breakerino/ecommerce/thankyou/payment_method/icon',
	null,
);

$billingAddressIcon = apply_filters(
	'breakerino/ecommerce/thankyou/billing_address/icon',
	null,
);

$shippingAddressIcon = apply_filters(
	'breakerino/ecommerce/thankyou/shipping_address/icon',
	null,
);

// Order data
$orderNumber = $order->get_order_number();

$orderDate = $order->get_date_created();
$orderDate = $orderDate
	? esc_html(wp_date('j. F Y', $orderDate->getTimestamp()))
	: null;

$paymentMethod = $order->get_payment_method_title();

$shippingMethods = $order->get_shipping_methods();

if (!empty($shippingMethods)) {
	$methodTitles = [];

	foreach ($shippingMethods as $shippingMethod) {
		$methodTitles[] = $shippingMethod->get_method_title();
	}

	$shippingMethod = esc_html(implode(', ', $methodTitles));
}

$addresses = [
	'billing' => [
		'company'  => $order->get_billing_company(),
		'name'     => trim(sprintf('%s %s', $order->get_billing_first_name(), $order->get_billing_last_name())),
		'address' => $order->get_billing_address_1(),
		'postcode' => $order->get_billing_postcode(),
		'city'     => $order->get_billing_city(),
		'country'  => $order->get_billing_country(),
		'email'    => $order->get_billing_email(),
		'phone'    => $order->get_billing_phone(),
	],
	'shipping' => [
		'name'     => trim(sprintf('%s %s', $order->get_shipping_first_name(), $order->get_shipping_last_name())),
		'address' => $order->get_shipping_address_1(),
		'city'     => $order->get_shipping_city(),
		'postcode' => $order->get_shipping_postcode(),
		'country'  => $order->get_shipping_country(),
		'company'  => $order->get_shipping_company(),
		'email'    => $order->get_billing_email(),
		'phone'    => $order->get_billing_phone(),
	],
];
?>

<div class="brk-ecommerce-thankyou">
	<div class="brk-ecommerce-thankyou-completion-notice">
		<?php if ($completionNoticeIcon): ?>
			<div class="brk-ecommerce-thankyou-completion-notice__icon">
				<?= $completionNoticeIcon; ?>
			</div>
		<?php endif; ?>

		<div class="brk-ecommerce-thankyou-completion-notice__content">
			<strong class="brk-ecommerce-thankyou-completion-notice__title mt-text mt-text--heading">Vaša objednávka bola úspešne dokončená.</strong>
			<span class="brk-ecommerce-thankyou-completion-notice__subtitle mt-text">Ďakujeme, že ste si vybrali <strong>Moris Trade</strong>. Vaše prémiové rumy budú <strong>čoskoro na ceste k vám</strong>.</span>
		</div>
	</div>

	<div class="brk-ecommerce-thankyou-order-info-boxes">
		<div class="brk-ecommerce-thankyou-order-info-box">
			<div class="brk-ecommerce-thankyou-order-info-box__icon">
				<?= $orderNumberIcon; ?>
			</div>
			<div class="brk-ecommerce-thankyou-order-info-box__content">
				<strong class="brk-ecommerce-thankyou-order-info-box__title">Čislo objednávky</strong>
				<span class="brk-ecommerce-thankyou-order-info-box__subtitle"><?= $orderNumber ?></span>
			</div>
		</div>

		<?php if ($orderDate): ?>
			<div class="brk-ecommerce-thankyou-order-info-box">
				<div class="brk-ecommerce-thankyou-order-info-box__icon">
					<?= $orderDateIcon; ?>
				</div>
				<div class="brk-ecommerce-thankyou-order-info-box__content">
					<strong class="brk-ecommerce-thankyou-order-info-box__title">Dátum objednávky</strong>
					<span class="brk-ecommerce-thankyou-order-info-box__subtitle"><?= $orderDate ?></span>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($paymentMethod): ?>
			<div class="brk-ecommerce-thankyou-order-info-box">
				<div class="brk-ecommerce-thankyou-order-info-box__icon">
					<?= $paymentMethodIcon; ?>
				</div>
				<div class="brk-ecommerce-thankyou-order-info-box__content">
					<strong class="brk-ecommerce-thankyou-order-info-box__title">Spôsob platby</strong>
					<span class="brk-ecommerce-thankyou-order-info-box__subtitle"><?= $paymentMethod ?> </span>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($shippingMethod): ?>
			<div class="brk-ecommerce-thankyou-order-info-box">
				<div class="brk-ecommerce-thankyou-order-info-box__icon">
					<?= $shippingMethodIcon; ?>
				</div>
				<div class="brk-ecommerce-thankyou-order-info-box__content">
					<strong class="brk-ecommerce-thankyou-order-info-box__title">Spôsob dopravy</strong>
					<span class="brk-ecommerce-thankyou-order-info-box__subtitle"><?= $shippingMethod ?></span>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<div class="brk-ecommerce-thankyou-order-summary brk-ecommerce-checkout-section brk-ecommerce-checkout-section--valid">
		<h2 class="brk-ecommerce-checkout-section-heading">
			<span class="brk-ecommerce-checkout-section-heading__text"><?= 'Vaša objednávka' ?></span>
		</h2>

		<div class="brk-ecommerce-checkout-section-content">
			<?php Helpers::get_view('thankyou/order-items', ['items' => $orderItems]); ?>
			<?php Helpers::get_view('checkout/subtotal', ['subtotal' => wc_price($orderSubtotal)]); ?>
			<?php Helpers::get_view('checkout/cart-fees', ['items' => Helpers::get_order_fees($order)]); ?>
			<?php Helpers::get_view('checkout/total', ['total' => Helpers::get_order_total_html($order)]); ?>
		</div>
	</div>

	<div class="brk-ecommerce-thankyou-order-addresses">
		<div class="brk-ecommerce-thankyou-order-address brk-ecommerce-thankyou-order-address--billing">
			<div class="brk-ecommerce-thankyou-order-address__header">
				<div class="brk-ecommerce-thankyou-order-address__icon">
					<?= $billingAddressIcon; ?>
				</div>
				<strong class="brk-ecommerce-thankyou-order-address__title mt-text"><?= __('Billing address', 'woocommerce'); ?></strong>
			</div>
			<div class="brk-ecommerce-thankyou-order-address__content">
				<?php if (!empty($addresses['billing']['company'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['billing']['company']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['billing']['name'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['billing']['name']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['billing']['address'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['billing']['address']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['billing']['postcode']) || !empty($addresses['billing']['city'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html(trim($addresses['billing']['postcode'] . ' ' . $addresses['billing']['city'])); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['billing']['country'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html(WC()->countries->countries[$addresses['billing']['country']] ?? $addresses['billing']['country']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['billing']['email'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['billing']['email']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['billing']['phone'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['billing']['phone']); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="brk-ecommerce-thankyou-order-address brk-ecommerce-thankyou-order-address--shipping">
			<div class="brk-ecommerce-thankyou-order-address__header">
				<div class="brk-ecommerce-thankyou-order-address__icon">
					<?= $shippingAddressIcon; ?>
				</div>
				<strong class="brk-ecommerce-thankyou-order-address__title mt-text"><?= __('Shipping address', 'woocommerce'); ?></strong>
			</div>
			<div class="brk-ecommerce-thankyou-order-address__content">
				<?php if (!empty($addresses['shipping']['name'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['shipping']['name']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['shipping']['address'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['shipping']['address']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['shipping']['postcode']) || !empty($addresses['shipping']['city'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html(trim($addresses['shipping']['postcode'] . ' ' . $addresses['shipping']['city'])); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['shipping']['country'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html(WC()->countries->countries[$addresses['shipping']['country']] ?? $addresses['shipping']['country']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['shipping']['email'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['shipping']['email']); ?></p>
				<?php endif; ?>
				<?php if (!empty($addresses['shipping']['phone'])): ?>
					<p class="mt-text mt-text--sm"><?= esc_html($addresses['shipping']['phone']); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>