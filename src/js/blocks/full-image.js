
/*
$(() => {
	let active = false;
	let animationId = null;

	// Cache jQuery objects once
	const $window = $(window);
	const items = $('.full-image picture img').map((i, el) => ({
		el,
		$picture: $(el).closest('picture'),
		$section: $(el).closest('.full-image'),
		lastTranslateY: 0
	})).get();

	// Apply parallax effect with RAF optimization
	const applyParallax = () => {
		if (active) {
			const updates = [];

			for (let i = 0; i < items.length; i++) {
				const o = items[i];
				const sectionRect = o.$section[0].getBoundingClientRect();
				const sectionTop = sectionRect.top;
				const sectionHeight = sectionRect.height;
				const windowHeight = window.innerHeight;

				// Calculate progress
				const progress = (windowHeight - sectionTop) / (windowHeight + sectionHeight) - 0.5;
				const translateY = progress * sectionHeight * 0.4;

				// Only update if value changed (avoid unnecessary DOM updates)
				if (Math.abs(translateY - o.lastTranslateY) > 0.1) {
					updates.push({ el: o.el, translateY });
					o.lastTranslateY = translateY;
				}
			}

			// Batch DOM updates
			updates.forEach(({ el, translateY }) => {
				el.style.transform = `translateY(${translateY}px)`;
			});
		}
		animationId = requestAnimationFrame(applyParallax);
	};

	// Check if parallax should be active based on viewport width
	const checkParallax = () => {
		const shouldBeActive = window.innerWidth >= 992;
		if (shouldBeActive !== active) {
			active = shouldBeActive;

			if (!active) {
				items.forEach(o => {
					o.el.style.transform = 'translateY(0px)';
					o.lastTranslateY = 0;
				});
			}
		}
	};

	// Debounced resize handler
	let resizeTimeout;
	const handleResize = () => {
		clearTimeout(resizeTimeout);
		resizeTimeout = setTimeout(checkParallax, 150);
	};

	// Initialize parallax
	checkParallax();
	$window.on('resize', handleResize);
	applyParallax();

	// Cleanup on page unload
	$window.on('beforeunload', () => {
		cancelAnimationFrame(animationId);
		$window.off('resize', handleResize);
	});
}) */