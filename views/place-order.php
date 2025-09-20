<?php
defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$orderButtonText = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );
?>

<div class="brk-checkout-place-order">
	<?php Helpers::get_view( 'terms' ); ?>

	<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

	<?= apply_filters( 'woocommerce_order_button_html', '<button type="submit" disabled="true" class="button alt' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $orderButtonText ) . '" data-value="' . esc_attr( $orderButtonText ) . '">' . esc_html( $orderButtonText ) . '</button>' ); // @codingStandardsIgnoreLine ?>

	<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

	<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
</div>