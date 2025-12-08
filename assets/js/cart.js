/*
	id: breakerino-ecommerce-cart
	name: Breakerino eCommerce | Cart
	type: script
	version: 0.1.0
	created: 07/12/2025
	updated: 07/12/2025
	conditions: is_cart
	dependencies: vendor-wc-cart
*/

// @ts-nocheck

function BreakerinoCartItem({ element, selectors, callback }) {
	const ELEMENT_TO_EVENT_MAP = {
		quantityInput: ['input'],
		quantityPlusButton: ['click'],
		quantityMinusButton: ['click']
	};

	const ELEMENT_TO_ACTION_MAP = {
		quantityInput: 'input',
		quantityPlusButton: 'increment',
		quantityMinusButton: 'decrement'
	};

	const CART_ITEM_ID_REGEX = /^cart\[(.*?)\]\[qty\]$/;

	this.cartItemID = null;

	this.quantity = {
		previous: null,
		current: null,
		min: null,
		max: null
	};

	this.elements = {
		quantityInput: null,
		quantityPlusButton: null,
		quantityMinusButton: null,
	};

	this.updateQuantity = (action) => {
		this.quantity.current = Number(this.elements.quantityInput.value) + (action ? action === 'increment' ? 1 : -1 : 0);

		if (this.quantity.current < this.quantity.min || this.quantity.current > this.quantity.max) {
			this.quantity.current = Number(this.elements.quantityInput.value);
			return;
		}
	};

	this.setQuantity = () => {
		this.quantity.current = Number(this.elements.quantityInput.value);
		this.quantity.previous = this.quantity.current;
		this.quantity.min = Number(this.elements.quantityInput.getAttribute('min')) || 1;
		this.quantity.max = Number(this.elements.quantityInput.getAttribute('max')) || Infinity;
	};

	this.setInputProps = function () {
		this.elements.quantityInput.setAttribute('min', this.quantity.min);
	};

	this.setElements = function () {
		for (const [name, selector] of Object.entries(selectors)) {
			this.elements[name] = element.querySelector(selector);
		}
	};

	this.setCartItemID = function () {
		const match = this.elements.quantityInput?.name?.match(CART_ITEM_ID_REGEX);

		if (!match?.[1] || match[1].length !== 32) {
			throw new Error('Cart item ID not found');
		}

		this.cartItemID = match[1];
	};

	this.bindEventHandlers = function () {
		for (const [name, element] of Object.entries(this.elements)) {
			if (!(element instanceof HTMLElement)) {
				continue;
			}

			ELEMENT_TO_EVENT_MAP[name].forEach(eventName => {
				element.addEventListener(eventName, (event) => callback(event, { action: ELEMENT_TO_ACTION_MAP[name], item: this }));
			});
		}
	};

	this.setElements();

	if (!(this.elements.quantityInput instanceof HTMLInputElement)) {
		return;
	}

	this.setQuantity();
	this.setInputProps();
	this.setCartItemID();
	this.bindEventHandlers();
}

function BreakerinoCart({ selectors, updateCartDelay, onCartUpdate: handleCartUpdate, debug = false }) {
	this.moduleName = 'BreakerinoCart';

	this.elements = {
		cartForm: null,
		cartItems: []
	};

	this.cartItems = [];

	this.debounceTimeout = null;

	this.setElements = function () {
		this.elements.cartForm = document.querySelector(selectors.cartForm);

		if (!(this.elements.cartForm instanceof HTMLElement)) {
			return;
		}

		this.elements.cartItems = [...this.elements.cartForm.querySelectorAll(selectors.cartItemWrapper)];
	};

	this.setCartItems = function () {
		this.cartItems = this.elements.cartItems.map(element => new BreakerinoCartItem({ element, selectors: selectors.cartItem, callback: this.handleEvent.bind(this) }));
	};

	this.isUpdatingCart = function () {
		return this.elements.cartForm.classList.contains('processing');
	};

	this.logMessage = function (message, type = 'debug', ...rest) {
		(type === 'debug' ? debug : true) && console[type in console && console[type] instanceof Function ? type : 'info'](`[${this.moduleName}]${type ? ` ${type.toUpperCase()}:` : ''} ${message}`, ...rest);
	};

	this.updateCart = function ({ event, action, item }) {
		this.logMessage('Updating cart', 'debug');
		clearTimeout(this.debounceTimeout);
		!this.isUpdatingCart() && handleCartUpdate({ event, action, item });
	};

	this.handleEvent = function (event, { action, item }) {
		console.debug({ event, action, item });
		clearTimeout(this.debounceTimeout);

		switch (action) {
			case 'input':
				item.updateQuantity(null);
				if (item.quantity.current === item.quantity.previous) break;
				this.debounceTimeout = setTimeout(() => this.updateCart({ event, action, item }), updateCartDelay);
				break;
			case 'increment':
			case 'decrement':
				item.updateQuantity(action);
				if (item.quantity.current === item.quantity.previous) break;
				this.debounceTimeout = setTimeout(() => this.updateCart({ event, action, item }), updateCartDelay);
				break;
			default:
				this.logMessage(`Unknown action: ${action}`, 'warn');
				break;
		}
	};

	this.init = function () {
		try {
			this.setElements();
			this.setCartItems();

			this.logMessage(`Module initialized.`);
		} catch (e) {
			this.logMessage(`Module failed to initialize.`, 'error', e);
		}
	};
}

(() => {
	const cart = new BreakerinoCart({
		debug: true,
		updateCartDelay: 500,
		onCartUpdate: ({ event, action, item }) => {
			try {
				switch (action) {
					case 'remove':
						console.log('remove', item);
						break;
					default:
						console.log('update', item);
						document.querySelector('[name="update_cart"]')?.removeAttribute('disabled');
						jQuery(document.body).trigger('wc_update_cart');
						break;
				}
			} catch (e) {
				console.error(e);
			}
		},
		selectors: {
			cartForm: '.brk-ecommerce-cart-form',
			cartItemWrapper: '.brk-ecommerce-cart-item',
			cartItem: {
				quantityInput: '.brk-quantity-selector input[type=number]',
				quantityPlusButton: '.brk-quantity-selector .action.plus',
				quantityMinusButton: '.brk-quantity-selector .action.minus',
			}
		}
	});

	document.addEventListener('DOMContentLoaded', () => {
		cart.init();

		jQuery(document.body).on('removed_from_cart updated_cart_totals wc_cart_emptied', () => {
			cart.init();
		});
	});
})();