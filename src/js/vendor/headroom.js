class Headroom {
	// SZ
	// Default configuration
	static defaults = {
		offset: 0,
		tolerance: {
			up: 0,
			down: 0,
		},
		scroller: typeof window !== 'undefined' ? window : null,
		classes: {
			initial: 'headroom',
			pinned: 'headroom--pinned',
			unpinned: 'headroom--unpinned',
			top: 'headroom--top',
			notTop: 'headroom--not-top',
			bottom: 'headroom--bottom',
			notBottom: 'headroom--not-bottom',
			frozen: 'headroom--frozen',
		},
	};

	// Private properties
	#parsedClasses = {};
	#scrollerAdapter = null;
	#rafId = null;
	#resizeObserver = null;

	constructor(element, options = {}) {
		if (!element) {
			throw new Error('Headroom: element is required.');
		}

		this.element = element;

		this.options = {
			...Headroom.defaults,
			...options,
			tolerance: Headroom.#normalizeUpDown(options.tolerance ?? Headroom.defaults.tolerance),
			offset: Headroom.#normalizeUpDown(options.offset ?? Headroom.defaults.offset),
			classes: {
				...Headroom.defaults.classes,
				...(options.classes || {}),
			},
		};

		this.scroller = this.options.scroller || window;
		this.#scrollerAdapter = Headroom.#createScroller(this.scroller);

		this.lastScrollY = this.#scrollerAdapter.scrollY();
		this.ticking = false;
		this.initialized = false;

		this.state = {
			pinned: true,
			top: true,
			bottom: false,
			frozen: false,
		};

		// Cache pre-split class lists for performance
		this.#parseClasses();

		// Bind event handlers
		this.onScroll = this.#onScroll.bind(this);
		this.update = this.#update.bind(this);
		this.onResize = this.#onResize.bind(this);
	}

	static #isWindow(obj) {
		return Boolean(obj && obj.document && obj.nodeType !== 9);
	}

	static #normalizeUpDown(val) {
		return val === Object(val) ? val : { down: val, up: val };
	}

	static #createScroller(scroller) {
		if (Headroom.#isWindow(scroller) || scroller === window) {
			return {
				scrollHeight: () => {
					const doc = document;
					const body = doc.body;
					const html = doc.documentElement;
					return Math.max(
						body.scrollHeight,
						html.scrollHeight,
						body.offsetHeight,
						html.offsetHeight,
						body.clientHeight,
						html.clientHeight
					);
				},
				height: () => window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
				scrollY: () => window.pageYOffset !== undefined ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop,
			};
		}

		return {
			scrollHeight: () => Math.max(scroller.scrollHeight, scroller.offsetHeight, scroller.clientHeight),
			height: () => Math.max(scroller.offsetHeight, scroller.clientHeight),
			scrollY: () => scroller.scrollTop,
		};
	}

	init() {
		if (this.initialized) {
			return this;
		}

		this.initialized = true;

		const { initial } = this.#parsedClasses;

		// Apply initial base class only (like in headroom.js original)
		if (initial.length) this.element.classList.add(...initial);

		// Defer scroll listener activation and state resolution
		setTimeout(() => {
			if (!this.initialized) return;

			this.scroller.addEventListener('scroll', this.onScroll, { passive: true });
			window.addEventListener('resize', this.onResize, { passive: true });

			if ('ResizeObserver' in window) {
				this.#resizeObserver = new ResizeObserver(this.onResize);
				this.#resizeObserver.observe(this.element);
			}

			this.update();
		}, 100);

		return this;
	}

	destroy() {
		if (!this.initialized) {
			return this;
		}

		this.scroller.removeEventListener('scroll', this.onScroll);
		window.removeEventListener('resize', this.onResize);

		if (this.#resizeObserver) {
			this.#resizeObserver.disconnect();
			this.#resizeObserver = null;
		}

		if (this.#rafId) {
			cancelAnimationFrame(this.#rafId);
			this.#rafId = null;
		}

		this.#removeAllClasses();
		this.initialized = false;

		return this;
	}

	pin() {
		this.#setPinned(true);
		return this;
	}

	unpin() {
		this.#setPinned(false);
		return this;
	}

	freeze() {
		this.state.frozen = true;
		const { frozen } = this.#parsedClasses;
		if (frozen.length) this.element.classList.add(...frozen);
		return this;
	}

	unfreeze() {
		this.state.frozen = false;
		const { frozen } = this.#parsedClasses;
		if (frozen.length) this.element.classList.remove(...frozen);
		return this;
	}

	#onScroll() {
		if (!this.ticking) {
			this.ticking = true;
			this.#rafId = requestAnimationFrame(this.update);
		}
	}

	#update() {
		this.ticking = false;

		if (!this.initialized || this.state.frozen) {
			return;
		}

		const scrollY = Math.round(this.#scrollerAdapter.scrollY());
		const height = this.#scrollerAdapter.height();
		const scrollHeight = this.#scrollerAdapter.scrollHeight();

		// Prevent bouncy scrolling behavior in OSX / iOS
		const isOutOfBounds = scrollY < 0 || scrollY + height > scrollHeight;
		if (isOutOfBounds) {
			return;
		}

		const delta = scrollY - this.lastScrollY;
		const direction = delta > 0 ? 'down' : 'up';
		const distance = Math.abs(delta);

		const isTop = scrollY <= this.options.offset[direction];
		const isBottom = scrollY + height >= scrollHeight;
		const toleranceExceeded = distance > this.options.tolerance[direction];

		// Apply state updates
		this.#setTopState(isTop);
		this.#setBottomState(isBottom);

		if (direction === 'down' && !isTop && toleranceExceeded) {
			this.#setPinned(false);
		} else if ((direction === 'up' && toleranceExceeded) || isTop) {
			this.#setPinned(true);
		}

		this.lastScrollY = scrollY;
	}

	#setPinned(pinned) {
		if (this.state.pinned === pinned && (this.element.classList.contains(this.#parsedClasses.pinned[0]) || this.element.classList.contains(this.#parsedClasses.unpinned[0]))) {
			return;
		}

		this.state.pinned = pinned;
		const { pinned: pinnedClass, unpinned: unpinnedClass } = this.#parsedClasses;

		if (pinned) {
			if (unpinnedClass.length) this.element.classList.remove(...unpinnedClass);
			if (pinnedClass.length) this.element.classList.add(...pinnedClass);
			this.options.onPin?.call(this);
		} else {
			if (pinnedClass.length) this.element.classList.remove(...pinnedClass);
			if (unpinnedClass.length) this.element.classList.add(...unpinnedClass);
			this.options.onUnpin?.call(this);
		}
	}

	#setTopState(isTop) {
		if (this.state.top === isTop && (this.element.classList.contains(this.#parsedClasses.top[0]) || this.element.classList.contains(this.#parsedClasses.notTop[0]))) {
			return;
		}

		this.state.top = isTop;
		const { top, notTop } = this.#parsedClasses;

		if (isTop) {
			if (notTop.length) this.element.classList.remove(...notTop);
			if (top.length) this.element.classList.add(...top);
			this.options.onTop?.call(this);
		} else {
			if (top.length) this.element.classList.remove(...top);
			if (notTop.length) this.element.classList.add(...notTop);
			this.options.onNotTop?.call(this);
		}
	}

	#setBottomState(isBottom) {
		if (this.state.bottom === isBottom && (this.element.classList.contains(this.#parsedClasses.bottom[0]) || this.element.classList.contains(this.#parsedClasses.notBottom[0]))) {
			return;
		}

		this.state.bottom = isBottom;
		const { bottom, notBottom } = this.#parsedClasses;

		if (isBottom) {
			if (notBottom.length) this.element.classList.remove(...notBottom);
			if (bottom.length) this.element.classList.add(...bottom);
			this.options.onBottom?.call(this);
		} else {
			if (bottom.length) this.element.classList.remove(...bottom);
			if (notBottom.length) this.element.classList.add(...notBottom);
			this.options.onNotBottom?.call(this);
		}
	}

	#onResize() {
		if (!this.ticking) {
			this.ticking = true;
			requestAnimationFrame(() => this.#update());
		}
	}

	#parseClasses() {
		this.#parsedClasses = {};

		for (const [key, value] of Object.entries(this.options.classes)) {
			if (typeof value === 'string') {
				this.#parsedClasses[key] = value.split(' ').filter(Boolean);
			} else if (Array.isArray(value)) {
				this.#parsedClasses[key] = value;
			} else {
				this.#parsedClasses[key] = [];
			}
		}
	}

	#removeAllClasses() {
		Object.values(this.#parsedClasses).forEach(classList => {
			if (classList.length) {
				this.element.classList.remove(...classList);
			}
		});
	}
}

export default Headroom;