import classnames from 'classnames/bind';

/**
 * WordPress dependencies
 */
const {
	InnerBlocks,
	useBlockProps,
	store: blockEditorStore,
} = wp.blockEditor;
const useInnerBlocksProps = wp.blockEditor?.useInnerBlocksProps || wp.blockEditor.__experimentalUseInnerBlocksProps;
const { useSelect } = wp.data;

const SwiperSlideEdit = ({ attributes: { templateLock = false, className }, clientId }) => {
	const blockProps = useBlockProps({
		className: classnames(className, 'swiper-slide')
	});

	const { columnsIds, hasChildBlocks, rootClientId } = useSelect(
		(select) => {
			const { getBlockOrder, getBlockRootClientId } = select(
				blockEditorStore
			);

			const rootId = getBlockRootClientId(clientId);

			return {
				hasChildBlocks: getBlockOrder(clientId).length > 0,
				rootClientId: rootId,
				columnsIds: getBlockOrder(rootId),
			};
		},
		[clientId]
	);

	const innerBlocksProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			templateLock,
			renderAppender: hasChildBlocks
				? undefined
				: InnerBlocks.ButtonBlockAppender,
		}
	);

	return <div {...innerBlocksProps} />;
}

export default SwiperSlideEdit;
