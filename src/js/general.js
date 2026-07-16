//const $ = window.jQuery; // it is defined global in vite not need anymore
import Headroom from 'headroom.js';

$(() => {
	inviewAnimate();
	initHeadroom();
	headerMenu();
})

const initHeadroom = () => {
	const $h = $('.site-header'), h = $h.outerHeight();
	//document.documentElement.style.setProperty('--hh', h + 'px'); //get dynamic header height and set it into the css variable var(--hh), but creates layout shift!!!
	$h.each((_, el) => new Headroom(el, { offset: h }).init());
} //$(window).on('resize', initHeadroom);

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
			headerBurger = $('.nav-trigger'),
			$body = $('body'),
			$htmlBody = $('html, body'),
			supportsScrollbarGutter = CSS.supports('scrollbar-gutter: stable'),
			scrollbarWidth = supportsScrollbarGutter ? 0 : window.innerWidth - document.documentElement.clientWidth;

	const toggleScrollbarCompensation = (isOpen) => {
		if (!scrollbarWidth) return;
		const padding = isOpen ? `${scrollbarWidth}px` : '';
		header.add($body).css('padding-right', padding);
	};

	const closeMenu = () => {
		if (!header.hasClass('nav-active')) return;
		$htmlBody.removeClass('noscroll');
		toggleScrollbarCompensation(false);
		headerBurger.removeClass('active').attr('aria-expanded', 'false');
		header.removeClass('nav-active');
		headerMenu.stop(true, true).fadeOut(300).removeClass('menu-active');
	};

	headerBurger.on('click', function () {
		const isOpen = $body.toggleClass('noscroll').hasClass('noscroll');
		toggleScrollbarCompensation(isOpen);
		headerBurger.toggleClass('active');
		header.toggleClass('nav-active');
		headerBurger.attr('aria-expanded', String(isOpen));
		if (isOpen) {
			headerMenu.stop(true, true).fadeIn(300).addClass('menu-active');
		} else {
			headerMenu.stop(true, true).fadeOut(300).removeClass('menu-active');
		}
	});

	headerMenu.on('click', 'a:not([href^="#"])', closeMenu);

	window.addEventListener('pageshow', (e) => {
		if (e.persisted) closeMenu();
	});

	const smoothScrollTo = ($target, offset = 0) => {
		if (!$target || !$target.length) return;
		const headerHeight = $('.site-header').outerHeight() || 0,
				 targetTop = $target.offset().top - headerHeight - offset;
		$htmlBody.stop(true).animate({ scrollTop: targetTop },500);
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
	$('.site-header').on('click', 'a[href="#"]', (e) => e.preventDefault());

	// Disable the click in MAIN over the "#" links and if the link has associated and ID will scroll to that ID if the ID exist
	$('.site-main').on('click', '.section a[href^="#"]', function (e) {
		e.preventDefault();
		const targetId = this.hash; if (!targetId || targetId === '#') return;
		const $target = $(targetId); if (!$target.length) return;
		smoothScrollTo($target, 10);
	});

	//Add class 'open' on menu-item with children on hover or click
	const hasHover = window.matchMedia('(hover: hover)').matches;
	if (hasHover) {
		$('.menu-item-has-children')
			.on('mouseenter', function() {
				$(this).addClass('open');
				$(this).children('a').attr('aria-expanded', 'true');
			})
			.on('mouseleave', function() {
				$(this).removeClass('open');
				$(this).children('a').attr('aria-expanded', 'false');
			});
	} else {
		$('.menu-item-has-children > a').on('click', function() {
			const $parent = $(this).parent();
			$('.menu-item-has-children').not($parent).removeClass('open');
			$(this).attr('aria-expanded', $parent.hasClass('open') ? 'true' : 'false');
			$parent .toggleClass('open');
		});

		$body.on('click', function(e) {
			if(!$(e.target).closest('.menu-item-has-children').length) {
				$('.menu-item-has-children').removeClass('open');
			}
		});
	}

}
