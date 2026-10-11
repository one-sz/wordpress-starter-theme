/**
 * Observes a target element's visibility within the viewport.
 *
 * @param {HTMLElement} targetElement - The DOM element to observe.
 * @param {Function} callback - Callback function executed on intersection change, receives (entry, observerInstance).
 * @param {Object} options - Configuration options (threshold, repeat).
 */
export function observeElementInView(targetElement, callback, options = {}) {
	const threshold = parseFloat(targetElement.dataset.threshold) || options.threshold || 0;
	const repeat = options.repeat || false; // Set to true if you want the animation to repeat on scroll

	const observer = new IntersectionObserver((entries, observerInstance) => {
		entries.forEach(entry => {
			callback(entry, observerInstance);

			// Unobserve once triggered unless repeat mode is enabled
			if (entry.isIntersecting && !repeat) {
				observerInstance.unobserve(targetElement);
			}
		});
	}, {
		threshold: threshold
	});

	observer.observe(targetElement);
}