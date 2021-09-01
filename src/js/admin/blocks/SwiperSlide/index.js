/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * WordPress Dependencies
 */
const { _x, __ } = wp.i18n;
const { name } = metadata;

export { metadata, name };

export const settings = {
	title: _x('Swiper Slide', 'block title'),
	keywords: [__('slide'), __('carousel'), __('swiper')],
	description: __(
		'Displays a Swiper Slide.'
	),
	edit,
	save,
};
