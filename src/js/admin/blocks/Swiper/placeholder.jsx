import { get } from 'lodash';

/**
 * WordPress dependencies
 */
const {
	useBlockProps,
	store: blockEditorStore,
} = wp.blockEditor;

const BlockVariationPicker = wp.blockEditor?.BlockVariationPicker || wp.blockEditor.__experimentalBlockVariationPicker

const { useDispatch, useSelect } = wp.data;
const {
	createBlocksFromInnerBlocksTemplate,
	store: blocksStore,
} = wp.blocks;


export default function Placeholder({ clientId, name, setAttributes }) {
	const { blockType, defaultVariation, variations } = useSelect(
		(select) => {
			const {
				getBlockVariations,
				getBlockType,
				getDefaultBlockVariation,
			} = select(blocksStore);

			return {
				blockType: getBlockType(name),
				defaultVariation: getDefaultBlockVariation(name, 'block'),
				variations: getBlockVariations(name, 'block'),
			};
		},
		[name]
	);
	const { replaceInnerBlocks } = useDispatch(blockEditorStore);
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<BlockVariationPicker
				icon={get(blockType, ['icon', 'src'])}
				label={get(blockType, ['title'])}
				variations={variations}
				onSelect={(nextVariation = defaultVariation) => {
					if (nextVariation.attributes) {
						setAttributes(nextVariation.attributes);
					}
					if (nextVariation.innerBlocks) {
						replaceInnerBlocks(
							clientId,
							createBlocksFromInnerBlocksTemplate(
								nextVariation.innerBlocks
							),
							true
						);
					}
				}}
				allowSkip
			/>
		</div>
	);
}
