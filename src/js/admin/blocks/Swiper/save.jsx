import classnames from 'classnames/bind';

const { Fragment } = wp.element;
const { InnerBlocks, useBlockProps } = wp.blockEditor;

const SwiperSave = ({ attributes }) => {
	const {
		slidesPerView,
		pagination,
		paginationPosition,
		navigation,
		scrollbar,
		blockId,
		previewVisibility
	} = attributes;

	const className = classnames(
		'swiper-container',
		`slides-per-column-${slidesPerView}`,
		`swiper-edges-${previewVisibility}`,
		{
			"swiper-has-pagination": pagination,
			"swiper-has-navigation": navigation,
			[`swiper-pagination-${paginationPosition}`]: paginationPosition && pagination
		}
	)

	const blockProps = useBlockProps.save({
		id: blockId,
		className
	});

	return (
		<div {...blockProps} id={blockId}>
			<div className="swiper-wrapper">
				<InnerBlocks.Content />
			</div>
			{pagination && <div className="swiper-pagination"></div>}
			{navigation && (
				<Fragment>
					<div className="swiper-button-prev"></div>
					<div className="swiper-button-next"></div>
				</Fragment>
			)}
			{scrollbar && <div className="swiper-scrollbar"></div>}
		</div>
	);
}

export default SwiperSave;
