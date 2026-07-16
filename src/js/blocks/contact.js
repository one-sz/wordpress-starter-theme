//import TomSelect from 'tom-select';
//import { observeElementInView } from '../vendor/inview-observer';

/* $(() => {
	initContactForm();
	//initTomSelect();
}); */

/* const initContactForm = () => {
	"use strict";

	// Uncomment in functions.php (add_filter( 'gform_disable_css', '__return_true' );) and load Gravity Forms CSS only when section comes in inview
	const gfStyles = [
		"/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-theme-reset.min.css",
		"/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-theme-foundation.min.css",
		"/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-theme-framework.min.css",
		"/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-orbital-theme.min.css"
	];

	// Cache for loaded stylesheet URLs to avoid duplicate checks
	const loadedStyles = new Set();
	// Pre-populate with already present stylesheets
	gfStyles.forEach(url => {
		if (document.querySelector(`link[href="${url}"]`)) { loadedStyles.add(url); }
	});

	let gfStylesLoaded = loadedStyles.size === gfStyles.length;

	const loadGFStyles = () => {
		if (gfStylesLoaded) return;
		// Filter only unloaded styles
		const stylesToLoad = gfStyles.filter(url => !loadedStyles.has(url));
		if (stylesToLoad.length === 0) { gfStylesLoaded = true; return; }

		const fragment = document.createDocumentFragment();
		stylesToLoad.forEach(url => {
			const link = document.createElement('link');
			link.rel = 'stylesheet';
			link.href = url;
			link.media = 'all';
			fragment.appendChild(link);
			loadedStyles.add(url);
		});

		document.head.appendChild(fragment);
		gfStylesLoaded = true;
	};

	// Single observer instance shared across all elements
	const callback = () => loadGFStyles();
	document.querySelectorAll('.contact').forEach(contact => {
		observeElementInView(contact, callback);
	});

}; */


//TomSelect for customized select fields
/* const initTomSelect = () => {
	"use strict";
	const initializedSelects = new WeakSet();
	function initTomSelect() {
		const selects = document.querySelectorAll('.form-wrap select:not(.tomselected)');
		selects.forEach((select) => {
			if (initializedSelects.has(select)) return;
			new TomSelect(select, {
				create: false,
				placeholder: 'Select one',
				maxOptions: false,
				searchField: [],
				controlInput: '<input readonly>',
				//plugins: ['remove_button'],//for multi-select to be able to remove added items
				render: {
					option: (data, escape) => data.value ? `<div>${escape(data.text)}</div>` : ''
				},
				onInitialize: function () {
					this.control_input.setAttribute('readonly', true);
					initializedSelects.add(select)
				}
			});
		});
	}
	const debouncedInit = () => requestAnimationFrame(initTomSelect);
	window.addEventListener('formRendered', debouncedInit);//set right in functions.php "formRendered"
	$(initTomSelect);
} */