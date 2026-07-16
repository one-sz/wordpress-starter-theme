// Inview with IntersectionObserver
export function observeElementInView(targetElement, callback) {
	const threshold = parseFloat(targetElement.dataset.threshold) || 0;
	const observer = new IntersectionObserver((entries, observerInstance) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				callback(entry); // Execute the callback when element is in view
				observer.unobserve(targetElement); // Unobserve once it's in view
			}
		});
	}, {
		threshold: threshold
	});
	observer.observe(targetElement);
}


/*
// Add it to JS file where you need the function
import { observeElementInView } from '../vendor/inview-observer';

// Select your target element
const target = document.querySelector('.element');

// Define what to do when the element is in view
const callback = (entry) => {
  console.log('Element is in view:', entry.target);
  // You can add additional logic here
};

// Call the function to observe the element
if (target) {
	observeElementInView(target, callback);
}
*/
