<?php

defined('ABSPATH') || exit;

use Breakerino\Checkout\Helpers;

$cartFees = Helpers::get_cart_fees();
$cartItems = Helpers::get_cart_items();

// Cart items
Helpers::get_view('cart-items', ['items' => $cartItems]);

// Subtotal
Helpers::get_view('subtotal');

// Cart fees
Helpers::get_view('cart-fees', ['items' => $cartFees ]);

// Total
Helpers::get_view('total');

// Place order
Helpers::get_view('place-order');
