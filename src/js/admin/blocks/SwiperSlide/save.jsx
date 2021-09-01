import classnames from 'classnames/bind';

const { InnerBlocks } = wp.blockEditor;

const SwiperSlideSave = (props) => (
	<div className={classnames(props?.attributes?.className, 'swiper-slide')}>
		<InnerBlocks.Content />
	</div>
)

export default SwiperSlideSave;
