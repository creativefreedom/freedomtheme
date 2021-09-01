/**
 * External dependencies
 */
import classnames from 'classnames';
import { dropRight, get, times } from 'lodash';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { PanelBody, RangeControl, Notice } = wp.components;

const {
	InspectorControls,
	__experimentalUseInnerBlocksProps: useInnerBlocksProps,
	BlockControls,
	BlockVerticalAlignmentToolbar,
	__experimentalBlockVariationPicker,
	useBlockProps,
	store: blockEditorStore,
} = wp.blockEditor;
const { withDispatch, useDispatch, useSelect } = wp.data;
const {
	createBlock,
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
			<__experimentalBlockVariationPicker
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
