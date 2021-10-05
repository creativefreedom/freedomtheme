import * as LatestContent from './admin/blocks/LatestContent';
import * as Swiper from './admin/blocks/Swiper';
import * as SwiperSlide from './admin/blocks/SwiperSlide';

const { registerBlockType, registerBlockStyle } = wp.blocks

/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 */
const registerBlock = (block) => {

	if (!block) {
		return;
	}
	const { metadata, settings, name } = block;

	registerBlockType(name, Object.assign(metadata, settings));
};
[LatestContent, Swiper, SwiperSlide].forEach(registerBlock);

// Block styles
wp.domReady(function () {

	// wp.blocks.registerBlockStyle("core/button", {
	// 	name: "secondary",
	// 	label: "Secondary"
	// });

	// wp.blocks.registerBlockStyle("core/button", {
	// 	name: "secondary-outline",
	// 	label: "Secondary Outline"
	// });

});


