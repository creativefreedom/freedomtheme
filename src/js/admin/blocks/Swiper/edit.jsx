/**
 * External dependencies
 */
import classnames from "classnames";
import { dropRight, get, times } from "lodash";
import Swiper from 'swiper'
import "swiper/swiper-bundle.css";
import "./styles.css";

/**
 * Internal dependencies
 */
import Placeholder from "./placeholder";
import {
	hasExplicitPercentColumnWidths,
	getMappedColumnWidths,
	getRedistributedColumnWidths,
	toWidthPrecision,
} from "./utils";

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Fragment, useEffect, useState } = wp.element;
const {
	InspectorControls,
	__experimentalUseInnerBlocksProps: useInnerBlocksProps,
	BlockControls,
	useBlockProps,
	store: blockEditorStore,
} = wp.blockEditor;
const {
	PanelBody,
	BaseControl,
	ToggleControl,
	RangeControl,
	Notice,
	ToolbarButton,
	ToolbarGroup,
	SelectControl,
} = wp.components;
const { withDispatch, useDispatch, useSelect } = wp.data;
const {
	createBlock,
} = wp.blocks;

const ALLOWED_BLOCKS = ["cf/swiper-slide"];
const INNER_TEMPLATE = [["cf/swiper-slide", {}]];

function SwiperEditContainer({
	attributes,
	setAttributes,
	updateSlides,
	clientId
}) {
	const {
		slidesPerView,
		pagination,
		navigation,
		scrollbar,
		autoplay,
		loop,
		speed,
		spaceBetween,
		paginationPosition,
		edgesVisible,
		previewVisibility
	} = attributes;

	const blockId = `block-${clientId}`;
	const getBlockElementSelector = (selector) => `#${blockId} ${selector}`;

	const { count } = useSelect(
		(select) => {
			return {
				count: select(blockEditorStore).getBlockCount(clientId),
			};
		},
		[clientId]
	);

	const [editMode, setEditMode] = useState(count === 0);
	const [swiper, setSwiper] = useState(null);

	const classes = classnames(
		"swiper-container",
		`slides-per-column-${slidesPerView}`,
		{
			"swiper-edit-mode": editMode,
			"swiper-edges-visible": edgesVisible
		}
	);

	const blockProps = useBlockProps({
		id: blockId,
		className: classes,
	});

	// Pass block id to save function
	useEffect(() => {
		// Prefix for non numeric selector
		setAttributes({ blockId });
	}, []);

	const innerBlocksProps = useInnerBlocksProps(
		{
			className: "swiper-wrapper",
		},
		{
			allowedBlocks: ALLOWED_BLOCKS,
			orientation: "horizontal",
			renderAppender: false,
			template: INNER_TEMPLATE,
			templateLock: false,
		}
	);

	const initialiseSwiper = () => {
		const settings = {
			speed: speed || 400,
			spaceBetween: spaceBetween || 100,
			autoplay,
			loop,
			slidesPerView,
		};

		if (pagination) {
			settings.pagination = {
				el: getBlockElementSelector(".swiper-pagination"),
			};
		}

		if (navigation) {
			settings.navigation = {
				nextEl: getBlockElementSelector(".swiper-button-next"),
				prevEl: getBlockElementSelector(".swiper-button-prev"),
			};
		}

		if (scrollbar) {
			settings.scrollbar = {
				el: getBlockElementSelector(".swiper-scrollbar"),
			};
		}
		// If previer Initialise Swiper
		setSwiper(new Swiper(`#${blockId}.swiper-container`, settings));
	};

	useEffect(() => {
		if (!editMode && !swiper) {
			initialiseSwiper();
		} else {
			// If edit mode Destroy Swiper instance
			if (swiper) {
				swiper.destroy();
				setSwiper(null);
			}
		}
	}, [editMode]);

	useEffect(() => {
		if (!editMode && !!swiper) {
			// Update Swiper
			swiper.destroy();
			initialiseSwiper();
		}
	}, [
		slidesPerView,
		pagination,
		navigation,
		scrollbar,
		autoplay,
		loop,
		speed,
		spaceBetween,
	]);

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					{editMode ? (
						<ToolbarButton
							icon={"visibility"}
							label={__("Preview")}
							onClick={() => setEditMode(!editMode)}
							showTooltip
						/>
					) : (
						<>
							<ToolbarButton
								icon={"edit"}
								label={__("Edit")}
								onClick={() => setEditMode(!editMode)}
								showTooltip
							/>
							<ToolbarButton
								icon={"arrow-left-alt"}
								label={__("Previous Slide")}
								onClick={() => swiper.slidePrev()}
								showTooltip
							/>
							<ToolbarButton
								icon={"arrow-right-alt"}
								label={__("Next Slide")}
								onClick={() => swiper.slideNext()}
								showTooltip
							/>
						</>
					)}
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={__("Slider Settings", "block-data-attribute")}
					initialOpen={true}
				>
					{editMode && (
						<>
							<RangeControl
								label={__("Slides")}
								value={count}
								onChange={(value) => updateSlides(count, value)}
								min={1}
								max={Math.max(15, count)}
							/>
							{count > 15 && (
								<Notice status="warning" isDismissible={false}>
									{__(
										"This slide count exceeds the recommended amount and may cause visual breakage."
									)}
								</Notice>
							)}
						</>
					)}
					{count > 1 && (
						<RangeControl
							label={"Slides Per View"}
							value={slidesPerView}
							onChange={(value) => setAttributes({ slidesPerView: value })}
							min={1}
							max={count}
						/>
					)}
					<RangeControl
						label={"Speed (seconds)"}
						value={speed / 100}
						onChange={(value) => setAttributes({ speed: value * 100 })}
						min={1}
						max={10}
					/>
					<RangeControl
						label={"Space between slides (px)"}
						value={spaceBetween}
						onChange={(value) => setAttributes({ spaceBetween: value })}
						min={10}
						max={100}
						step={10}
					/>
					<BaseControl>
						<ToggleControl
							label={__("Autoplay?")}
							checked={autoplay}
							onChange={(value) => setAttributes({ autoplay: value })}
							help={
								!!autoplay ? __("Will autoplay.") : __("Will not autoplay.")
							}
						/>
						<ToggleControl
							label={__("Loop slides?")}
							checked={loop}
							onChange={(value) => setAttributes({ loop: value })}
							help={!!loop ? __("Will loop.") : __("Will not loop.")}
						/>
						<ToggleControl
							label={__("Include scrollbar?")}
							checked={!!scrollbar}
							onChange={() => setAttributes({ scrollbar: !scrollbar })}
							help={!!scrollbar ? __("Has scrollbar.") : __("No scrollbar.")}
						/>
						<SelectControl
							label={__('Slide Preview Visibility')}
							value={previewVisibility}
							onChange={value => setAttributes({ previewVisibility: value })}
							options={[
								{ value: "none", label: "Hidden" },
								{ value: "semi", label: "Semi-transparent" },
								{ value: "visible", label: "Visible" }
							]}
						/>
					</BaseControl>
				</PanelBody>
				<PanelBody
					title={__("Navigation", "block-data-attribute")}
					initialOpen={false}
				>
					<ToggleControl
						label={__("Include navigation?")}
						checked={!!navigation}
						onChange={() => setAttributes({ navigation: !navigation })}
						help={!!navigation ? __("Has navigation.") : __("No navigation.")}
					/>
				</PanelBody>
				<PanelBody
					title={__("Pagination", "block-data-attribute")}
					initialOpen={false}
				>
					<ToggleControl
						label={__("Include pagination?")}
						checked={!!pagination}
						onChange={() => setAttributes({ pagination: !pagination })}
						help={!!pagination ? __("Has pagination.") : __("No pagination.")}
					/>
					{pagination &&
						<SelectControl
							label={__('Pagination Position')}
							value={paginationPosition}
							onChange={position => setAttributes({ paginationPosition: position })}
							options={[
								{ value: "top-right", label: "Top Right" },
								{ value: "top-left", label: "Top Left" },
								{ value: "bottom-right", label: "Bottom Right" },
								{ value: "bottom-left", label: "Bottom Left" },
							]}
						/>
					}
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div {...innerBlocksProps} />
				{pagination && <div className={classnames("swiper-pagination", `swiper-pagination--${paginationPosition}`)}></div>}
				{navigation && (
					<Fragment>
						<div className="swiper-button-prev"></div>
						<div className="swiper-button-next"></div>
					</Fragment>
				)}
				{scrollbar && <div className="swiper-scrollbar"></div>}
			</div>
		</>
	);
}

const SwiperEditContainerWrapper = withDispatch(
	(dispatch, ownProps, registry) => ({
		/**
		 * Updates the column count, including necessary revisions to child Column
		 * blocks to grant required or redistribute available space.
		 *
		 * @param {number} previousSlides Previous column count.
		 * @param {number} newSlides      New column count.
		 */
		updateSlides(previousSlides, newSlides) {
			const { clientId } = ownProps;
			const { replaceInnerBlocks } = dispatch(blockEditorStore);
			const { getBlocks } = registry.select(blockEditorStore);

			let innerBlocks = getBlocks(clientId);
			const hasExplicitWidths = hasExplicitPercentColumnWidths(innerBlocks);

			// Redistribute available width for existing inner blocks.
			const isAddingSlide = newSlides > previousSlides;

			if (isAddingSlide && hasExplicitWidths) {
				// If adding a new column, assign width to the new column equal to
				// as if it were `1 / slides` of the total available space.
				const newSlideWidth = toWidthPrecision(100 / newSlides);

				// Redistribute in consideration of pending block insertion as
				// constraining the available working width.
				const widths = getRedistributedColumnWidths(
					innerBlocks,
					100 - newSlideWidth
				);

				innerBlocks = [
					...getMappedColumnWidths(innerBlocks, widths),
					...times(newSlides - previousSlides, () => {
						return createBlock("cf/swiper-slide", {});
					}),
				];
			} else if (isAddingSlide) {
				innerBlocks = [
					...innerBlocks,
					...times(newSlides - previousSlides, () => {
						return createBlock("cf/swiper-slide");
					}),
				];
			} else {
				// The removed column will be the last of the inner blocks.
				innerBlocks = dropRight(innerBlocks, previousSlides - newSlides);

				if (hasExplicitWidths) {
					// Redistribute as if block is already removed.
					const widths = getRedistributedColumnWidths(innerBlocks, 100);

					innerBlocks = getMappedColumnWidths(innerBlocks, widths);
				}
			}

			replaceInnerBlocks(clientId, innerBlocks);
		},
	})
)(SwiperEditContainer);

const SwiperEdit = (props) => {
	const { clientId } = props;
	const hasInnerBlocks = useSelect(
		(select) => select(blockEditorStore).getBlocks(clientId).length > 0,
		[clientId]
	);
	const Component = SwiperEditContainerWrapper;

	return <Component {...props} />;
};

export default SwiperEditContainerWrapper;
