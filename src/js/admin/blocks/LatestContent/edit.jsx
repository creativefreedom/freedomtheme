/**
 * External dependencies
 */
import { get, includes, invoke, isUndefined, pickBy, intersection } from 'lodash';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import {
	MIN_EXCERPT_LENGTH,
	MAX_EXCERPT_LENGTH,
	MAX_POSTS_COLUMNS,
} from './constants';
import { pin } from './icons';

/**
 * WordPress dependencies
 */
const { useState, RawHTML, useEffect, useRef } = wp.element;
const {
	BaseControl,
	PanelBody,
	Placeholder,
	QueryControls,
	RadioControl,
	RangeControl,
	Spinner,
	ToggleControl,
	FormTokenField
} = wp.components;
const apiFetch = wp.apiFetch;
const { addQueryArgs } = wp.url;
const { __ } = wp.i18n;
const {
	InspectorControls,
	BlockAlignmentToolbar,
	__experimentalImageSizeControl: ImageSizeControl,
	useBlockProps,
	store: blockEditorStore,
} = wp.blockEditor;
const { useSelect } = wp.data;

const { store: coreStore } = wp.coreData;

/**
 * Module Constants
 */
const CATEGORIES_LIST_QUERY = {
	per_page: -1,
};

const POSTS_QUERY = {
	per_page: -1,
};

const MAX_POSTS_SUGGESTIONS = 30;

export default function LatestContentEdit({ attributes, setAttributes }) {
	const {
		postsToShow,
		postsToInclude,
		order,
		orderBy,
		categories,
		displayFeaturedImage,
		displayPostContentRadio,
		displayPostContent,
		displayPostCategory,
		offsetPosts,
		columns,
		excerptLength,
		featuredImageAlign,
		featuredImageSizeSlug,
		featuredImageSizeWidth,
		featuredImageSizeHeight,
		addLinkToFeaturedImage,
	} = attributes;

	const {
		imageSizeOptions,
		latestPosts,
		defaultImageWidth,
		defaultImageHeight,
	} = useSelect(
		(select) => {

			const { getEntityRecords, getMedia } = select(coreStore);
			const { getSettings } = select(blockEditorStore);
			const { imageSizes, imageDimensions } = getSettings();

			const catIds =
				categories && categories.length > 0
					? categories.map(({ id }) => id)
					: [];

			let postIds;
			if (postsToInclude && postsToInclude?.length > 0) {
				postIds = postsToInclude.map(({ id }) => id)
			}

			const latestPostsQuery = pickBy(
				{
					categories: catIds,
					order,
					orderby: orderBy,
					offset: offsetPosts,
					per_page: postsToShow,
					include: postIds
				},
				(value) => !isUndefined(value)
			);

			const posts = getEntityRecords(
				'postType',
				'post',
				latestPostsQuery
			);

			return {
				defaultImageWidth: get(
					imageDimensions,
					[featuredImageSizeSlug, 'width'],
					0
				),
				defaultImageHeight: get(
					imageDimensions,
					[featuredImageSizeSlug, 'height'],
					0
				),
				imageSizeOptions: imageSizes
					.filter(({ slug }) => slug !== 'full')
					.map(({ name, slug }) => ({
						value: slug,
						label: name,
					})),
				latestPosts: !Array.isArray(posts)
					? posts
					: posts.map((post) => {
						if (!post.featured_media) return post;

						const image = getMedia(post.featured_media);
						let url = get(
							image,
							[
								'media_details',
								'sizes',
								featuredImageSizeSlug,
								'source_url',
							],
							null
						);
						if (!url) {
							url = get(image, 'source_url', null);
						}
						const featuredImageInfo = {
							url,
							// eslint-disable-next-line camelcase
							alt: image?.alt_text,
						};
						return { ...post, featuredImageInfo };
					}),
			};
		},
		[
			featuredImageSizeSlug,
			postsToShow,
			order,
			orderBy,
			offsetPosts,
			categories,
			postsToInclude
		]
	);

	const [categoriesList, setCategoriesList] = useState([]);


	const categorySuggestions = categoriesList.reduce(
		(accumulator, category) => ({
			...accumulator,
			[category.name]: category,
		}),
		{}
	);
	const selectCategories = (tokens) => {
		const hasNoSuggestion = tokens.some(
			(token) =>
				typeof token === 'string' && !categorySuggestions[token]
		);
		if (hasNoSuggestion) {
			return;
		}
		// Categories that are already will be objects, while new additions will be strings (the name).
		// allCategories nomalizes the array so that they are all objects.
		const allCategories = tokens.map((token) => {
			return typeof token === 'string'
				? categorySuggestions[token]
				: token;
		});
		// We do nothing if the category is not selected
		// from suggestions.
		if (includes(allCategories, null)) {
			return false;
		}
		setAttributes({ categories: allCategories });
	};

	const [postsList, setPostsList] = useState([]);

	const postSuggestions = postsList.reduce(
		(accumulator, post) => {

			if (categories?.length > 0 && post?.categories?.length > 0) {
				// Check if has a selected post category
				const catIds = categories.map(({ id }) => id);
				const intersecting = intersection(catIds, post.categories)?.length > 0;
				if (!intersecting) return accumulator;
			}

			return ({
				...accumulator,
				[post.title.rendered]: post,
			})
		},
		{}
	);

	const selectPosts = (tokens) => {
		const hasNoSuggestion = tokens.some(
			(token) =>
				typeof token === 'string' && !postSuggestions[token]
		);

		if (hasNoSuggestion) return;

		// Categories that are already will be objects, while new additions will be strings (the name).
		// allCategories nomalizes the array so that they are all objects.
		const allPosts = tokens.map((token) => {
			return typeof token === 'string'
				? postSuggestions[token]
				: token;
		});

		// We do nothing if the category is not selected
		// from suggestions.
		if (includes(allPosts, null)) return false;

		setAttributes({ postsToInclude: allPosts });
	}

	const isStillMounted = useRef();

	useEffect(() => {
		isStillMounted.current = true;

		apiFetch({
			path: addQueryArgs(`/wp/v2/categories`, CATEGORIES_LIST_QUERY),
		})
			.then((data) => {
				if (isStillMounted.current) {
					setCategoriesList(data);
				}
			})
			.catch(() => {
				if (isStillMounted.current) {
					setCategoriesList([]);
				}
			});

		apiFetch({
			path: addQueryArgs(`/wp/v2/posts`, POSTS_QUERY)
		}).then((data) => {
			if (isStillMounted.current) {
				setPostsList(data);
			}
		})
			.catch(() => {
				if (isStillMounted.current) {
					setPostsList([]);
				}
			});

		return () => {
			isStillMounted.current = false;
		};
	}, []);

	const hasPosts = !!latestPosts?.length;
	const inspectorControls = (
		<InspectorControls>
			<PanelBody title={__('Post content settings')}>
				<ToggleControl
					label={__('Post content')}
					checked={displayPostContent}
					onChange={(value) =>
						setAttributes({ displayPostContent: value })
					}
				/>
				{displayPostContent && (
					<RadioControl
						label={__('Show:')}
						selected={displayPostContentRadio}
						options={[
							{ label: __('Excerpt'), value: 'excerpt' },
							{
								label: __('Full post'),
								value: 'full_post',
							},
						]}
						onChange={(value) =>
							setAttributes({
								displayPostContentRadio: value,
							})
						}
					/>
				)}
				{displayPostContent &&
					displayPostContentRadio === 'excerpt' && (
						<RangeControl
							label={__('Max number of words in excerpt')}
							value={excerptLength}
							onChange={(value) =>
								setAttributes({ excerptLength: value })
							}
							min={MIN_EXCERPT_LENGTH}
							max={MAX_EXCERPT_LENGTH}
						/>
					)}
			</PanelBody>

			<PanelBody title={__('Post meta settings')}>
				<ToggleControl
					label={__('Display categories')}
					checked={displayPostCategory}
					onChange={(value) =>
						setAttributes({ displayPostCategory: value })
					}
				/>
			</PanelBody>

			<PanelBody title={__('Featured image settings')}>
				<ToggleControl
					label={__('Display featured image')}
					checked={displayFeaturedImage}
					onChange={(value) =>
						setAttributes({ displayFeaturedImage: value })
					}
				/>
				{displayFeaturedImage && (
					<>
						<ImageSizeControl
							onChange={(value) => {
								const newAttrs = {};
								if (value.hasOwnProperty('width')) {
									newAttrs.featuredImageSizeWidth =
										value.width;
								}
								if (value.hasOwnProperty('height')) {
									newAttrs.featuredImageSizeHeight =
										value.height;
								}
								setAttributes(newAttrs);
							}}
							slug={featuredImageSizeSlug}
							width={featuredImageSizeWidth}
							height={featuredImageSizeHeight}
							imageWidth={defaultImageWidth}
							imageHeight={defaultImageHeight}
							imageSizeOptions={imageSizeOptions}
							onChangeImage={(value) =>
								setAttributes({
									featuredImageSizeSlug: value,
									featuredImageSizeWidth: undefined,
									featuredImageSizeHeight: undefined,
								})
							}
						/>
						<BaseControl className="block-editor-image-alignment-control__row">
							<BaseControl.VisualLabel>
								{__('Image alignment')}
							</BaseControl.VisualLabel>
							<BlockAlignmentToolbar
								value={featuredImageAlign}
								onChange={(value) =>
									setAttributes({
										featuredImageAlign: value,
									})
								}
								controls={['left', 'center', 'right']}
								isCollapsed={false}
							/>
						</BaseControl>
						<ToggleControl
							label={__('Add link to featured image')}
							checked={addLinkToFeaturedImage}
							onChange={(value) =>
								setAttributes({
									addLinkToFeaturedImage: value,
								})
							}
						/>
					</>
				)}
			</PanelBody>

			<PanelBody title={__('Sorting and filtering')}>
				{postSuggestions && (
					<FormTokenField
						key="query-controls-categories-select"
						label={__('Include posts')}
						value={
							postsToInclude &&
							postsToInclude.map((item) => ({
								id: item.id,
								value: item?.title?.rendered || item.value,
							}))
						}
						suggestions={Object.keys(postSuggestions)}
						onChange={selectPosts}
						maxSuggestions={MAX_POSTS_SUGGESTIONS}
					/>
				)}
				<QueryControls
					{...{ order, orderBy }}
					numberOfItems={postsToShow}
					onOrderChange={(value) =>
						setAttributes({ order: value })
					}
					onOrderByChange={(value) =>
						setAttributes({ orderBy: value })
					}
					onNumberOfItemsChange={(value) =>
						setAttributes({ postsToShow: value })
					}
					categorySuggestions={categorySuggestions}
					onCategoryChange={selectCategories}
					selectedCategories={categories}
				/>
				<RangeControl
					label={__('Offset posts')}
					value={offsetPosts}
					onChange={(value) =>
						setAttributes({ offsetPosts: value })
					}
					min={0}
					max={10}
					required
				/>
				<RangeControl
					label={__('Columns')}
					value={columns}
					onChange={(value) =>
						setAttributes({ columns: value })
					}
					min={1}
					max={
						!hasPosts
							? MAX_POSTS_COLUMNS
							: Math.min(
								MAX_POSTS_COLUMNS,
								latestPosts.length
							)
					}
					required
				/>
			</PanelBody>
		</InspectorControls>
	);

	const blockProps = useBlockProps({
		className: classnames({
			'wp-block-cf-latest-content__list': true,
			'has-categories': displayPostCategory,
			[`columns-${columns}`]: true,
		}),
	});

	if (!hasPosts) {
		return (
			<div {...blockProps}>
				{inspectorControls}
				<Placeholder icon={pin} label={__('Latest Content')}>
					{!Array.isArray(latestPosts) ? (
						<Spinner />
					) : (
						__('No posts found.')
					)}
				</Placeholder>
			</div>
		);
	}

	// Removing posts from display should be instant.
	const displayPosts =
		latestPosts.length > postsToShow
			? latestPosts.slice(0, postsToShow)
			: latestPosts;

	const getCategory = catId => categoriesList.find(cat => cat.id === catId);

	return (
		<div>
			{inspectorControls}
			<ul {...blockProps}>
				{displayPosts.map((post, i) => {
					const titleTrimmed = invoke(post, [
						'title',
						'rendered',
						'trim',
					]);
					let excerpt = post.excerpt.rendered;

					const excerptElement = document.createElement('div');
					excerptElement.innerHTML = excerpt;

					excerpt =
						excerptElement.textContent ||
						excerptElement.innerText ||
						'';

					const {
						featuredImageInfo: {
							url: imageSourceUrl,
							alt: featuredImageAlt,
						} = {},
					} = post;
					const imageClasses = classnames({
						'wp-block-cf-latest-content__featured-image': true,
						[`align${featuredImageAlign}`]: !!featuredImageAlign,
					});
					const renderFeaturedImage =
						displayFeaturedImage && imageSourceUrl;
					const featuredImage = renderFeaturedImage && (
						<img
							src={imageSourceUrl}
							alt={featuredImageAlt}
							style={{
								maxWidth: featuredImageSizeWidth,
								maxHeight: featuredImageSizeHeight,
							}}
						/>
					);
						
					const postDate = new Date(post.date);

					const needsReadMore =
						excerptLength < excerpt.trim().split(' ').length &&
						post.excerpt.raw === '';

					const postExcerpt = needsReadMore ? (
						<>
							{excerpt
								.trim()
								.split(' ', excerptLength)
								.join(' ')}
							{ /* translators: excerpt truncation character, default …  */}
							{__(' … ')}
							<a rel="noopener noreferrer">
								{__('Read more')}
							</a>
						</>
					) : (
						excerpt
					);

					return (
						<li key={i}>
							{renderFeaturedImage && (
								<div className={imageClasses}>
									{addLinkToFeaturedImage ? (
										<a
											
											rel="noreferrer noopener"
										>
											{featuredImage}
										</a>
									) : (
										featuredImage
									)}
								</div>
							)}
							<div className="wp-block-cf-latest-content__content">
								{(displayPostCategory && post.categories.length) && (
									<div className="wp-block-cf-latest-content__post-categories mb-1">
										{post.categories.map(categoryId => (
											<span className="tag">{getCategory(categoryId)?.name}</span>
										))}
									</div>
								)}
								<h3>
									<a rel="noreferrer noopener">
										{titleTrimmed ? (
											<RawHTML>{titleTrimmed}</RawHTML>
										) : (
											__('(no title)')
										)}
									</a>
								</h3>
								{displayPostContent &&
									displayPostContentRadio === 'excerpt' && (
										<div className="wp-block-cf-latest-content__post-excerpt">
											{postExcerpt}
										</div>
									)}
								{displayPostContent &&
									displayPostContentRadio === 'full_post' && (
										<div className="wp-block-cf-latest-content__post-full-content">
											<RawHTML key="html">
												{post.content.raw.trim()}
											</RawHTML>
										</div>
									)}
								<div className="wp-block-cf-latest-content__post-date text-muted small mt-auto">
									{postDate.toDateString()}
								</div>
							</div>
						</li>
					);
				})}
			</ul>
		</div>
	);
}
