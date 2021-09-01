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
	title: _x('Swiper', 'block title'),
	keywords: [__('slider'), __('carousel'), __('swiper')],
	description: __(
		'Displays a Swiper block.'
	),
	edit,
	save,
};
