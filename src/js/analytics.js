$(() => { initGTM.init(); })

// GTM – lazy load optimized for Core Web Vitals
const initGTM = (() => {
	let gtmLoaded = false;

	const loadGTM = () => {
		if (gtmLoaded || !window.GTM_ID) return;
		gtmLoaded = true;
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push({
			'gtm.start': new Date().getTime(),
			event: 'gtm.js'
		});
		const script = document.createElement('script');
		script.async = true;
		script.src = 'https://www.googletagmanager.com/gtm.js?id=' + window.GTM_ID;
		document.head.appendChild(script);
	};

	const init = () => {
		// 1. Load when the browser is idle (best for Core Web Vitals)
		if ('requestIdleCallback' in window) {
			requestIdleCallback(loadGTM, { timeout: 3000 });
		} else {
			setTimeout(loadGTM, 2000);
		}
		// 2. Fallback if the user interacts sooner
		['scroll', 'mousemove', 'touchstart', 'click'].forEach(event => {
			window.addEventListener(event, loadGTM, { once: true, passive: true });
		});
	};
	return { init };
})();