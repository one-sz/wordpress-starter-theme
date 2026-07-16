import { observeElementInView } from '../vendor/inview-observer';
import Swiper from 'swiper/bundle';

$(() => {
	"use strict";

	const carousels = document.querySelectorAll('.amenities-carousel');

	carousels.forEach((carousel) => {
		const callback = () => {
			if (carousel.dataset.initialized === "true") return;
			new Swiper(carousel.querySelector('.swiper'), {
				mousewheel: false,
				initialSlide: 0,
				speed: 700,
				//effect: "fade",
				//fadeEffect: { crossFade: true },//must be used when effect fade is enabled
				grabCursor: false,
				centeredSlides: false,
				slidesPerView: '1',
				spaceBetween: 16,
				loop: true,
				lazy: {
					loadPrevNext: true,
				},
				pagination: {
					el: carousel.querySelector(".swiper-pagination"),
					clickable: true,
				},
				navigation: {
					nextEl: carousel.querySelector(".swiper-button-next"),
					prevEl: carousel.querySelector(".swiper-button-prev"),
				},
			});

			carousel.dataset.initialized = "true"; // marked as already initialized
		};

		observeElementInView(carousel, callback);
	});
});
