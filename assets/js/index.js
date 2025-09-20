/*
	id: breakerino-checkout
	name: Breakerino Checkout
	type: script
	conditions: is_checkout
	version: 0.1.0
	created: 20/07/25
	updated: 12/09/25
	dependencies: wc-checkout
*/

// @ts-nocheck

const BreakerinoCheckout = {
	/** ------------------------------------------------------------
	 * Constants
	 * -------------------------------------------------------------
	 */
	MODULE_NAME: 'BreakerinoCheckout',
	MODULE_VERSION: '1.0.0',

	/** ------------------------------------------------------------
	 * Props
	 * -------------------------------------------------------------
	 */
	props: {
		debug: false,
		classes: {
			wrapper: 'brk-checkout',
			loader: 'brk-checkout-loader',
			section: 'brk-checkout-section',
			conditionalSection: 'brk-checkout-conditional-section',
			fieldGroup: 'brk-checkout-form-field-group',
			field: 'brk-checkout-form-field',
			methods: 'brk-checkout-methods',
			method: 'brk-checkout-method',
		},
		dataPrefix: 'data-brk-checkout',
		breakpoints: {
			mobile: 768,
		},
	},

	/** ------------------------------------------------------------
	 * Elements
	 * -------------------------------------------------------------
	 */
	elements: {
		wrapper: null,
	},

	/** ------------------------------------------------------------
	 * State
	 * -------------------------------------------------------------
	 */
	state: {
		initialized: false,
		checkoutInitialized: false,
		isLoading: false,
		isCheckoutValid: false,
		sections: {},
		conditionalSections: {},
		fieldGroups: {},
		fields: {},
		methods: {},
		checkboxes: {},
	},

	/** ------------------------------------------------------------
	 * Functions
	 * -------------------------------------------------------------
	 */

	/**
	 * Log message
	 *
	 * @param {string} message
	 * @param {string} type
	 * @param {any} data
	 *
	 * @returns void
	 */
	logMessage(message, type = 'info', ...data) {
		if (type === 'debug' && !this.getProp('debug')) {
			return;
		}

		console[type in console && console[type] instanceof Function ? type : 'info'](`[${this.MODULE_NAME}]${type ? ` ${type.toUpperCase()}:` : ''} ${message}`, ...data);
	},

	scrollIntoView(element, offset = 0) {
		const anchorElem = element instanceof HTMLElement ? element : typeof element === 'string' ? document.querySelector(element) : null;

		if (!anchorElem) return false;

		const anchorElemScrollY = anchorElem.getBoundingClientRect().top;

		window.scrollTo({
			top: anchorElemScrollY + window.pageYOffset - offset,
			behavior: 'smooth',
		});
		return true;
	},

	/**
	 * Validate field
	 *
	 * @param {object} field
	 * @returns boolean
	 */
	validateField(field) {
		return field.state.required ? !!field.state.value : true;
	},

	/**
	 * Validate field group
	 *
	 * @param {object} fieldGroup
	 * @returns boolean|null
	 */
	validateFieldGroup(fieldGroup) {
		if (fieldGroup.refs.conditionalSection) {
			const conditionalSections = this.getState('conditionalSections');

			if (fieldGroup.refs.conditionalSection in conditionalSections && !conditionalSections[fieldGroup.refs.conditionalSection].state.active) {
				return null;
			}
		}

		let fields = this.getState('fields');

		if (!fields || fields === {}) {
			return true;
		}

		fields = Object.values(fields).filter((field) => field.refs.fieldGroup === fieldGroup.id);

		if (!fields || !Array.isArray(fields)) {
			return true;
		}

		for (const field of fields) {
			if (!field.state.valid) {
				return false;
			}
		}

		return true;
	},

	/**
	 * Validate fields section
	 *
	 * @param {object} section
	 * @returns boolean
	 */
	validateFieldsSection(section) {
		const fieldGroups = this.getState('fieldGroups');
		const sectionFieldGroups = Object.values(fieldGroups).filter((fieldGroup) => fieldGroup.refs.section === section.id);

		for (const sectionFieldGroup of sectionFieldGroups) {
			if (sectionFieldGroup.state.valid === false) {
				return false;
			}
		}

		return true;
	},

	/**
	 * Validate methods section
	 *
	 * @param {object} section
	 * @returns boolean
	 */
	validateMethodsSection(section) {
		const methods = this.getState('methods');
		const sectionMethod = Object.values(methods).filter((method) => method.refs.section === section.id)?.[0];

		if (!sectionMethod) {
			return true;
		}

		const selectedChoice = Object.values(sectionMethod.state.choices).some((choice) => choice.state.selected);

		return !!selectedChoice;
	},

	/**
	 * Validate summary section
	 *
	 * @param {object} section
	 * @returns boolean
	 */
	validateSummarySection(section) {
		const checkboxes = this.getState('checkboxes');

		for (const checkbox of Object.values(checkboxes)) {
			if (!checkbox.state.checked) {
				return false;
			}
		}

		return true;
	},

	/**
	 * Validate section
	 *
	 * @param {object} section
	 * @returns boolean
	 */
	validateSection(section) {
		switch (section.type) {
			case 'fields':
				return this.validateFieldsSection(section);
			case 'methods':
				return this.validateMethodsSection(section);
			case 'summary':
				return this.validateSummarySection(section);
		}

		return true;
	},

	/** ------------------------------------------------------------
	 * Getters
	 * -------------------------------------------------------------
	 */

	/**
	 * Get state
	 *
	 * @param {string} path
	 * @returns any
	 */
	getState(path) {
		const keys = path.split('.');

		let value = this.state;

		for (const key of keys) {
			if (!(key in value)) {
				throw new Error(`Invalid state "${key}"`);
			}

			value = value[key];
		}

		return value;
	},

	/**
	 * Get prop
	 *
	 * @param {string} path
	 * @returns an\y
	 */
	getProp(path) {
		const keys = path.split('.');

		let value = this.props;

		for (const key of keys) {
			if (!(key in value)) {
				throw new Error(`Invalid prop "${key}"`);
			}

			value = value[key];
		}

		return value;
	},

	getSection(wrapper, refs = {}) {
		const section = {
			id: null,
			refs,
			type: null,
			elements: { wrapper },
			state: { valid: false },
		};

		if (!(section.elements.wrapper instanceof HTMLElement)) {
			throw new Error('Invalid section wrapper element.');
		}

		section.id = section.elements.wrapper.getAttribute(`${this.getProp('dataPrefix')}-id`);

		if (!section.id) {
			throw new Error('Missing section ID.');
		}

		section.type = section.elements.wrapper.getAttribute(`${this.getProp('dataPrefix')}-type`);

		if (!section.type) {
			throw new Error('Missing section type.');
		}

		section.state.valid = this.validateSection(section);

		return section;
	},

	/**
	 * Get conditional section
	 *
	 * @param {HTMLElement} wrapper
	 * @param {object} refs
	 * @returns object
	 */
	getConditionalSection(wrapper, refs = {}) {
		const conditionalSection = {
			id: null,
			refs,
			elements: {
				wrapper,
				input: null,
			},
			state: {
				active: false,
			},
		};

		conditionalSection.id = conditionalSection.elements.wrapper.getAttribute(`${this.getProp('dataPrefix')}-id`);

		if (!conditionalSection.id) {
			throw new Error('Missing conditional section ID.');
		}

		if (!(conditionalSection.elements.wrapper instanceof HTMLElement)) {
			throw new Error('Invalid conditional section wrapper element.');
		}

		conditionalSection.elements.input = conditionalSection.elements.wrapper.querySelector('input[type=checkbox]');

		if (!(conditionalSection.elements.input instanceof HTMLInputElement)) {
			throw new Error('Invalid conditional section checkbox element.');
		}

		conditionalSection.state.active = conditionalSection.elements.input.checked;

		return conditionalSection;
	},

	/**
	 * Get field group
	 *
	 * @param {HTMLElement} wrapper
	 * @param {object} refs
	 * @returns object
	 */
	getFieldGroup(wrapper, refs = {}) {
		const fieldGroup = {
			id: null,
			refs,
			elements: { wrapper },
			state: {
				valid: null,
			},
		};

		if (!(fieldGroup.elements.wrapper instanceof HTMLElement)) {
			throw new Error('Invalid field group wrapper element.');
		}

		fieldGroup.id = fieldGroup.elements.wrapper.getAttribute(`${this.getProp('dataPrefix')}-id`);

		if (!fieldGroup.id) {
			throw new Error('Missing field group ID.');
		}

		fieldGroup.state.valid = this.validateFieldGroup(fieldGroup);

		return fieldGroup;
	},

	/**
	 * Get field
	 *
	 * @param {HTMLElement} wrapper
	 * @param {object} refs
	 * @returns object
	 */
	getField(wrapper, refs = {}) {
		const field = {
			id: null,
			refs,
			elements: {
				wrapper,
				input: null,
			},
			state: {
				valid: null,
				value: null,
			},
		};

		if (!(field.elements.wrapper instanceof HTMLElement)) {
			throw new Error('Invalid field wrapper element.');
		}

		field.elements.input = field.elements.wrapper.querySelector('input, textarea');

		if (!(field.elements.input instanceof HTMLElement)) {
			throw new Error('Invalid field input element.');
		}

		field.id = field.elements.input.getAttribute(`${this.getProp('dataPrefix')}-id`);

		if (!field.id) {
			throw new Error('Missing field ID');
		}

		field.state.required = field.elements.input.hasAttribute(`${this.getProp('dataPrefix')}-required`);

		field.state.value = field.elements.input.value;
		field.state.valid = this.validateField(field);

		return field;
	},

	/**
	 * Get method
	 *
	 * @param {HTMLElement} wrapper
	 * @param {object} refs
	 * @returns object
	 */
	getMethod(wrapper, refs = {}) {
		const method = {
			type: null,
			refs,
			elements: {
				wrapper,
			},
			state: {
				choices: {},
			},
		};

		if (!(method.elements.wrapper instanceof HTMLElement)) {
			throw new Error('Invalid method wrapper element.');
		}

		method.type = method.elements.wrapper.getAttribute(`${this.getProp('dataPrefix')}-type`);

		if (!method.type) {
			throw new Error('Missing method type.');
		}

		method.state.choices = Object.fromEntries(
			[...method.elements.wrapper.querySelectorAll(`.${this.getProp('classes.method')}`)].map((wrapper) => {
				const choice = {
					id: null,
					elements: {
						wrapper,
						input: null,
					},
					state: {
						selected: null,
					},
				};

				choice.id = choice.elements.wrapper.getAttribute(`${this.getProp('dataPrefix')}-id`);

				if (!choice.id) {
					throw new Error('Missing method choice type.');
				}

				choice.elements.input = choice.elements.wrapper.querySelector('input[type=radio]');

				if (!(choice.elements.input instanceof HTMLElement)) {
					throw new Error('Invalid method choice input element.');
				}

				choice.state.selected = choice.elements.input.checked;

				return [choice.id, choice];
			})
		);

		return method;
	},
	/**
	 * Get checkbix
	 *
	 * @param {HTMLInputElement} checkbox
	 * @param {object} refs
	 * @returns object
	 */
	getCheckbox(input, refs = {}) {
		const checkbox = {
			id: input.name,
			refs,
			elements: { input },
			state: {
				checked: input.checked,
			},
		};

		return checkbox;
	},

	/**
	 * Get field group conditional section
	 *
	 * @param {HTMLElement} fieldGroupWrapper
	 * @deprecated
	 * @returns object|null
	 */
	getFieldGroupConditionalSection(fieldGroupWrapper) {
		const wrapper = fieldGroupWrapper.parentElement.parentElement;

		if (!wrapper.classList.contains(this.getProp('classes.conditionalSection'))) {
			return null;
		}

		const conditionalSectionID = wrapper.getAttribute(`${this.getProp('dataPrefix')}-id`);

		return this.getState(`conditionalSections.${conditionalSectionID}`);
	},

	/** ------------------------------------------------------------
	 * Setters
	 * -------------------------------------------------------------
	 */

	/**
	 * Set props
	 *
	 * @param {Object} props
	 * @throws {Error} If invalid property name or type
	 * @returns void
	 */
	setProps(props) {
		for (const [name, value] of Object.entries(props)) {
			if (!(name in this.props)) {
				throw new Error(`Invalid property name "${name}"`);
			}

			if (typeof value !== typeof this.props[name]) {
				throw new Error(`Invalid property type "${typeof value}" (expected ${typeof this.props[name]})`);
			}

			this.props[name] = value;
		}
	},

	setState(path, value) {
		const keys = path.split('.');

		const newState = { ...this.state };
		let pointer = newState;

		for (let i = 0; i < keys.length - 1; i++) {
			const key = keys[i];

			pointer[key] = { ...pointer[key] };
			pointer = pointer[key];
		}

		// Set the final value
		pointer[keys[keys.length - 1]] = value;

		this.logMessage(`Setting state "${path}" to`, 'debug', value);
		this.state = newState;
	},

	/**
	 * Set elements
	 *
	 * @throws {Error} If invalid element name or type
	 * @returns void
	 */
	setElements() {
		this.elements.wrapper = document.querySelector(`.${this.getProp('classes.wrapper')}`);

		if (!this.elements.wrapper) {
			throw new Error(`Invalid checkout wrapper element.`);
		}

		this.elements.loader = document.querySelector(`.${this.getProp('classes.loader')}`);

		if (!this.elements.loader) {
			throw new Error(`Invalid checkout loader element.`);
		}
	},

	/**
	 * Set sections
	 *
	 * @returns void
	 */
	setSections() {
		const wrappers = [...this.elements.wrapper.querySelectorAll(`.${this.getProp('classes.section')}`)];

		for (const wrapper of wrappers) {
			const section = this.getSection(wrapper);
			this.setState(`sections.${section.id}`, section);
		}
	},

	/**
	 * Set conditional sections
	 *
	 * @returns void
	 */
	setConditionalSections() {
		const sections = this.getState('sections');

		for (const [sectionID, section] of Object.entries(sections)) {
			const wrappers = [...section.elements.wrapper.querySelectorAll(`.${this.getProp('classes.conditionalSection')}`)];

			for (const wrapper of wrappers) {
				const conditionalSection = this.getConditionalSection(wrapper, { section: sectionID });
				this.setState(`conditionalSections.${conditionalSection.id}`, conditionalSection);
			}
		}
	},

	/**
	 * Set field groups
	 *
	 * @returns void
	 */
	setFieldGroups() {
		const sections = this.getState('sections');

		for (const [sectionID, section] of Object.entries(sections)) {
			const wrappers = [...section.elements.wrapper.querySelectorAll(`.${this.getProp('classes.fieldGroup')}`)];

			for (const wrapper of wrappers) {
				const conditionalSection = this.getFieldGroupConditionalSection(wrapper);
				const fieldGroup = this.getFieldGroup(wrapper, { section: sectionID, conditionalSection: conditionalSection?.id ?? null });

				this.setState(`fieldGroups.${fieldGroup.id}`, fieldGroup);
			}
		}
	},

	/**
	 * Set fields
	 *
	 * @returns void
	 */
	setFields() {
		const fieldGroups = this.getState('fieldGroups');

		for (const [fieldGroupID, fieldGroup] of Object.entries(fieldGroups)) {
			const wrappers = [...fieldGroup.elements.wrapper.querySelectorAll(`.${this.getProp('classes.field')}`)];

			for (const wrapper of wrappers) {
				const field = this.getField(wrapper, { section: fieldGroup.refs.section, fieldGroup: fieldGroupID });
				this.setState(`fields.${field.id}`, field, { fieldGroup: fieldGroupID });
			}
		}
	},

	/**
	 * Set methods
	 *
	 * @returns void
	 */
	setMethods() {
		const sections = this.getState('sections');

		for (const [sectionID, section] of Object.entries(sections)) {
			const wrappers = [...section.elements.wrapper.querySelectorAll(`.${this.getProp('classes.methods')}`)];

			for (const wrapper of wrappers) {
				const method = this.getMethod(wrapper, { section: sectionID });
				this.setState(`methods.${method.type}`, method);
			}
		}
	},

	/**
	 * Set methods
	 *
	 * @returns void
	 */
	setMethods() {
		const sections = this.getState('sections');

		for (const [sectionID, section] of Object.entries(sections)) {
			const wrappers = [...section.elements.wrapper.querySelectorAll(`.${this.getProp('classes.methods')}`)];

			for (const wrapper of wrappers) {
				const method = this.getMethod(wrapper, { section: sectionID });
				this.setState(`methods.${method.type}`, method);
			}
		}
	},

	/**
	 * Set checkboxes
	 *
	 * @returns void
	 */
	setCheckboxes() {
		const sections = this.getState('sections');

		if (!('summary' in sections)) {
			throw new Error('Missing "summary" section.');
		}

		const checkboxes = sections.summary.elements.wrapper.querySelectorAll('input[type=checkbox]');
		
		for (const input of checkboxes) {
			const checkbox = this.getCheckbox(input, { section: sections.summary.id });
			this.setState(`checkboxes.${checkbox.id}`, checkbox);
		}
	},

	/** ------------------------------------------------------------
	 * Handlers
	 * -------------------------------------------------------------
	 */

	/**
	 * Update conditional sections
	 *
	 * @returns void
	 */
	updateConditionalSections() {
		this.setConditionalSections();
		
		requestAnimationFrame(this.renderConditionalSections.bind(this));
	},

	/**
	 * Update fields
	 *
	 * @returns void
	 */
	updateFields() {
		this.setFields();
	},

	/**
	 * Update field groups
	 *
	 * @returns void
	 */
	updateFieldGroups() {
		this.setFieldGroups();
	},

	/**
	 * Update methods
	 *
	 * @returns void
	 */
	updateMethods() {
		this.setMethods();

		requestAnimationFrame(this.renderMethods.bind(this));
	},

	/**
	 * Update sections
	 *
	 * @returns void
	 */
	updateSections() {
		this.setSections();

		const sections = this.getState('sections');

		const sectionsCount = Object.values(sections).length;
		const validSectionsCount = Object.values(sections).reduce((count, section) => count + (section.state.valid ? 1 : 0), 0);

		this.setState('isCheckoutValid', validSectionsCount === sectionsCount);

		requestAnimationFrame(this.renderSections.bind(this));
		requestAnimationFrame(this.renderSummarySection.bind(this));
	},
	
	/**
	 * Update checkboxes
	 *
	 * @returns void
	 */
	updateCheckboxes() {
		this.setCheckboxes();
	},

	/**
	 * Update loader
	 *
	 * @returns void
	 */
	updateLoader() {
		requestAnimationFrame(this.renderLoader.bind(this));
	},

	/**
	 * Update loader
	 *
	 * @returns void
	 */
	updateCheckout() {
		jQuery(document.body).trigger('update_checkout');
	},

	/**
	 * Update
	 *
	 * @returns void
	 */
	update() {
		this.updateSections();
		this.updateConditionalSections();
		this.updateFields();
		this.updateFieldGroups();
		this.updateMethods();
		this.updateCheckboxes();
		this.updateSections();
		this.updateLoader();

		this.logMessage('Update', 'debug', this);
	},

	/**
	 * Render conditional sections
	 *
	 * @returns void
	 */
	renderConditionalSections() {
		const conditionalSections = this.getState('conditionalSections');

		for (const conditionalSection of Object.values(conditionalSections)) {
			conditionalSection.elements.wrapper.classList.toggle(`${this.getProp('classes.conditionalSection')}--active`, conditionalSection.state.active);
		}
	},

	/**
	 * Render methods
	 *
	 * @returns void
	 */
	renderMethods() {
		const methods = this.getState('methods');

		for (const method of Object.values(methods)) {
			for (const choice of Object.values(method.state.choices)) {
				choice.elements.wrapper.classList.toggle(`${this.getProp('classes.method')}--selected`, choice.state.selected);
			}
		}
	},

	/**
	 * Render sections
	 *
	 * @returns void
	 */
	renderSections() {
		let sections = this.getState('sections');
		sections = Object.entries(sections);

		for (const [, section] of sections) {
			section.elements.wrapper.classList.toggle(`${this.getProp('classes.section')}--valid`, section.state.valid);
		}

		outerSections: for (const [index, [sectionID, section]] of sections.entries()) {
			if (section.state.valid) {
				section.elements.wrapper.classList.toggle(`${this.getProp('classes.section')}--collapsed`, false);

				// // Scroll to first section if is the last seciton is valid
				if (window.innerWidth < this.getProp('breakpoints.mobile') && index + 1 === sections.length) {
					//this.scrollIntoView(section.elements.wrapper, 24);
				}
				// else {
				// 	// Scroll to next invalid section
				// 	this.scrollIntoView(sections[index + 1][1].elements.wrapper, 24);
				// }
				continue;
			}

			for (const [innerIndex, [innerSectionID, innerSection]] of sections.entries()) {
				// Expand all previous valid sections
				if (innerIndex <= index || innerSection.type === 'summary') {
					innerSection.elements.wrapper.classList.toggle(`${this.getProp('classes.section')}--collapsed`, false);
					continue;
				}

				// Collapse all invalid sections
				innerSection.elements.wrapper.classList.toggle(`${this.getProp('classes.section')}--collapsed`, true);
			}

			break outerSections;
		}
	},

	/**
	 * Render summary section
	 *
	 * @returns void
	 */
	renderSummarySection() {
		const submitButton = this.elements.wrapper.querySelector('[type=submit]');

		if (!(submitButton instanceof HTMLButtonElement)) {
			throw new Error('Invalid submit button element.');
		}

		const isCheckoutValid = this.getState('isCheckoutValid');

		submitButton.disabled = !isCheckoutValid;
	},

	/**
	 * Render summary section
	 *
	 * @returns void
	 */
	renderLoader() {
		this.elements.loader.classList.toggle(`${this.getProp('classes.loader')}--visible`, this.getState('isLoading'));
	},

	/**
	 * Bind event handlers
	 *
	 * @returns void
	 */
	bindEventHandlers() {
		const fields = this.getState('fields');
		const conditionalSections = this.getState('conditionalSections');
		const methods = this.getState('methods');
		const checkboxes = this.getState('checkboxes');
		
		// Fields
		for (const field of Object.values(fields)) {
			if (field.elements.input.hasAttribute(`${this.getProp('dataPrefix')}-initialized`)) {
				continue;
			}

			field.elements.input.addEventListener('input', this.update.bind(this));
			field.elements.input.setAttribute(`${this.getProp('dataPrefix')}-initialized`, '');
		}

		// Conditional sections
		for (const conditionalSection of Object.values(conditionalSections)) {
			if (conditionalSection.elements.input.hasAttribute(`${this.getProp('dataPrefix')}-initialized`)) {
				continue;
			}

			conditionalSection.elements.input.addEventListener('change', this.update.bind(this));
			conditionalSection.elements.input.setAttribute(`${this.getProp('dataPrefix')}-initialized`, '');
		}

		// Methods
		for (const method of Object.values(methods)) {
			for (const choice of Object.values(method.state.choices)) {
				if (choice.elements.input.hasAttribute(`${this.getProp('dataPrefix')}-initialized`)) {
					continue;
				}

				choice.elements.input.addEventListener('change', this.update.bind(this));
				choice.elements.input.addEventListener('change', this.updateCheckout.bind(this));

				choice.elements.input.setAttribute(`${this.getProp('dataPrefix')}-initialized`, '');
			}
		}
		
		// Checkboxes
		for (const checkbox of Object.values(checkboxes)) {
			if (checkbox.elements.input.hasAttribute(`${this.getProp('dataPrefix')}-initialized`)) {
				continue;
			}

			checkbox.elements.input.addEventListener('change', this.update.bind(this));
			checkbox.elements.input.setAttribute(`${this.getProp('dataPrefix')}-initialized`, '');
		}
	},

	/**
	 * Initialize checkout
	 *
	 * @returns void
	 */
	initCheckout() {
		jQuery(document.body).on('init_checkout update_checkout', () => {
			setTimeout(() => {
				this.update();
				this.setState('isLoading', true);
			}, 10);
		});

		jQuery(document.body).on('updated_checkout checkout_error', () => {
			this.update();
			this.bindEventHandlers();
			this.setState('isLoading', false);
		});
	},

	/**
	 * Initialize module
	 *
	 * @returns void
	 */
	init(props) {
		if (this.getState('initialized')) {
			return;
		}

		// Set props
		this.setProps(props);

		// Set elements
		this.setElements();

		// Set sections
		this.setSections();

		// Set sections
		this.setConditionalSections();

		// Set sections
		this.setFieldGroups();

		// Set fields
		this.setFields();

		// Set methods
		this.setMethods();

		// Set methods
		this.setCheckboxes();

		// Bind event handlers
		this.bindEventHandlers();

		// Initialize checkout
		this.initCheckout();

		// Initialize module
		this.logMessage(`Module init (v${this.MODULE_VERSION})`, 'debug', this);
		this.setState('initialized', true);
	},
};

document.addEventListener('DOMContentLoaded', () => {
	BreakerinoCheckout.init({ debug: true, breakpoints: { mobile: 810 } });
});
