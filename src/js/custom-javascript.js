(() => {
	class CFApp {
		constructor() {
			this.swiperInit();
		}

		swiperInit() {
			const swipers = window?.CF?.context || {};
			const defaultOptions = {
				autoplay: false,
				loop: false,
				slidesPerView: 1,
				speed: 400,
				spaceBetween: 10,
				// autoHeight: true,
			};

			if (!Object.values(swipers).length) return;

			Object.entries(swipers).forEach(([id, settings]) => {

				if (settings?.slidesPerView > 1) {
					settings.breakpoints = {
						640: {
							slidesPerView: settings.slidesPerView,
							spaceBetween: settings.spaceBetween || defaultOptions.spaceBetween
						}
					}

					settings.slidesPerView = defaultOptions.slidesPerView;
					settings.spaceBetween = defaultOptions.slidesPerView;
				}
				const swiper = new Swiper(`#${id}`, { ...defaultOptions, ...settings })
			})
		}
	}

	new ETUApp;
})();
