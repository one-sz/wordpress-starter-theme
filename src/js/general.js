//const $ = window.jQuery; // it is defined global in vite not need anymore
//import Headroom from 'headroom.js'; //original version based on jQuery, but not so fast
import Headroom from './vendor/headroom.js'; //vanilla ES6 built, optimized for performance

$(() => {
	inviewAnimate();
	initHeadroom();
	headerMenu();
})

const initHeadroom = () => {
	document.querySelectorAll('.site-header').forEach( header => { new Headroom(header, { offset: header.offsetHeight, }).init(); } );
}

const inviewAnimate = () => {
	const ivElements = document.querySelectorAll(`
		.inview-animate--fade,
		.inview-animate--move-up,
		.inview-animate--move-down,
		.inview-animate--move-left,
		.inview-animate--move-right,
		.inview-animate,
		.inview-animate--scale
	`);

	ivElements.forEach(ivElement => {
		const threshold = parseFloat(ivElement.getAttribute('data-threshold')) || 0.05; // Default 0.05 (0.05 = 5% ... 1 = 100%)
		const options = { threshold: threshold };

		const observer = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {

				if (entry.isIntersecting) {
					const visibleRatio = entry.intersectionRatio;

					if (visibleRatio >= threshold) {
						entry.target.classList.add('inview-active');

						// Once entered into the inview, it should be removed from the observer (for optimization)
						observer.unobserve(entry.target);

					}/* else {
						//Removing the class after the element is no longer visible if we want to animate the element every time it becomes visible
						entry.target.classList.remove('inview-active');
					} */

				}

			});
		}, options);

		observer.observe(ivElement);
	});
}

const headerMenu = () => {
	const header = $('.site-header'),
			headerMenu = $('.nav-wrap'),
			navWrapEl = headerMenu.get(0),
			headerBurger = $('.nav-trigger'),
			$body = $('body'),
			$htmlBody = $('html, body'),
			$menuItems = $('.menu-item-has-children');

	let startX = 0;
	let startY = 0;
	let currentX = 0;
	let isSwiping = false;
	let isHorizontalSwipe = null;

	if (!navWrapEl) return;

	// Safe Popover trigger handlers
	const showNavPopover = () => { if (typeof navWrapEl.showPopover === 'function' && !navWrapEl.matches(':popover-open')) navWrapEl.showPopover(); };

	const hideNavPopover = () => { if (typeof navWrapEl.hidePopover === 'function' && navWrapEl.matches(':popover-open')) navWrapEl.hidePopover(); };

	// Listen to Popover native toggle state
	navWrapEl.addEventListener('toggle', (e) => {
		const isOpen = e.newState === 'open';
		headerBurger.toggleClass('active', isOpen).attr('aria-expanded', String(isOpen));
		header.toggleClass('nav-active', isOpen);
		$body.toggleClass('noscroll', isOpen);
		if (!isOpen) {
			navWrapEl.style.transform = '';
			headerMenu.removeClass('is-dragging');
		}
	});

	// Close Popover drawer when clicking navigation links
	headerMenu.on('click', 'a:not([href^="#"])', hideNavPopover);

	window.addEventListener('pageshow', (e) => { if (e.persisted) hideNavPopover(); });

	// Touch & Swipe gestures logic (Hardware Accelerated via translate3d)
	const handleTouchStart = (e) => {
		if (window.innerWidth >= 992) return;
		const touch = e.touches[0];
		startX = currentX = touch.clientX;
		startY = touch.clientY;
		isHorizontalSwipe = null;
		const isMenuOpen = navWrapEl.matches(':popover-open'),
				isRightEdge = startX >= window.innerWidth - 50;//px sliding to activate drawer from right side
		isSwiping = isMenuOpen || isRightEdge;
	};

	const handleTouchMove = (e) => {
		if (!isSwiping) return;
		const touch = e.touches[0],
				diffX = touch.clientX - startX,
				diffY = touch.clientY - startY;
		if (isHorizontalSwipe === null) { isHorizontalSwipe = Math.abs(diffX) > Math.abs(diffY); }
		if (!isHorizontalSwipe) return;
		currentX = touch.clientX;
		const isMenuOpen = navWrapEl.matches(':popover-open');
		if (isMenuOpen) {
			if (diffX > 0) { headerMenu.addClass('is-dragging'); navWrapEl.style.transform = `translate3d(${diffX}px, 0, 0)`; }
		} else if (diffX < 0) {
			showNavPopover();
			headerMenu.addClass('is-dragging');
			const drawerWidth = navWrapEl.offsetWidth || window.innerWidth * 0.95;
			const currentTranslateX = Math.max(0, drawerWidth + diffX);
			navWrapEl.style.transform = `translate3d(${currentTranslateX}px, 0, 0)`;
		}
	};

	const handleTouchEnd = () => {
		if (!isSwiping) return;
		isSwiping = false;
		headerMenu.removeClass('is-dragging');
		const diffX = currentX - startX,
				isMenuOpen = navWrapEl.matches(':popover-open'),
				threshold = 20;//how much to swipe to close the menu
		if (isMenuOpen) {
			if (diffX > threshold) { hideNavPopover(); } else { navWrapEl.style.transform = ''; }
		} else {
			if (diffX < -threshold) { navWrapEl.style.transform = ''; } else { hideNavPopover(); }
		}
	};

	document.addEventListener('touchstart', handleTouchStart, { passive: true });
	document.addEventListener('touchmove', handleTouchMove, { passive: true });
	document.addEventListener('touchend', handleTouchEnd, { passive: true });
	document.addEventListener('touchcancel', handleTouchEnd, { passive: true });

	const smoothScrollTo = ($target, offset = 0) => {
		if (!$target?.length) return;
		const headerHeight = header.outerHeight() || 0,
				targetTop = $target.offset().top - headerHeight - offset;
		$htmlBody.stop(true).animate({ scrollTop: targetTop }, 500);
	};

	// Smooth scroll to hash on page load
	const { hash } = window.location;

	if (hash && hash.length > 1) {
		const $target = $(hash);
		if ($target.length) {
			$htmlBody.scrollTop(0);
			requestAnimationFrame(() => {
				requestAnimationFrame(() => {
					smoothScrollTo($target, 150);
					$target.blur();
				});
			});
			history.replaceState(null, '', window.location.pathname + window.location.search);
		}
	}

	// Prevent default on header anchor links
	header.on('click', 'a[href="#"]', (e) => e.preventDefault());

	// Disable click in MAIN over "#" links with smooth scroll
	$('.site-main').on('click', '.section a[href^="#"]', function(e) {
		e.preventDefault();
		const targetId = this.hash;
		if (!targetId || targetId === '#') return;
		const $target = $(targetId);
		if (!$target.length) return;
		smoothScrollTo($target, 10);
	});

	// Submenu handling
	const hasHover = window.matchMedia('(hover: hover)').matches;

	if (hasHover) {
		//Mouse
		$menuItems
			.on('mouseenter', function() {
				$(this).addClass('open').children('a').attr('aria-expanded', 'true');
			})
			.on('mouseleave', function() {
				$(this).removeClass('open').children('a').attr('aria-expanded', 'false');
			});
	} else {
		//Touch
		$menuItems.children('a').on('click', function() {
			const $parent = $(this).parent(),
					isOpen = !$parent.hasClass('open');
			$menuItems.not($parent).removeClass('open').children('a').attr('aria-expanded', 'false');
			$parent.toggleClass('open', isOpen);
			$(this).attr('aria-expanded', String(isOpen));
		});
		$body.on('click', function(e) {
			if (!$(e.target).closest('.menu-item-has-children').length) {
				$menuItems.removeClass('open').children('a').attr('aria-expanded', 'false');
			}
		});
	}

}

//Used for import/require OLD JS library if you get Uncaught TypeError: $(...).js-library-name is not a function
/* window.$ = window.jQuery = require('jquery');
require('js-library-name'); */