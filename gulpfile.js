// Defining requirements
const gulp = require('gulp');
const plumber = require('gulp-plumber');
const sass = require('gulp-sass');
const babel = require('gulp-babel');
const cleanCSS = require('gulp-clean-css');
const autoprefixer = require('autoprefixer');
const rename = require('gulp-rename');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const browserSync = require('browser-sync').create();
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');
const clean = require('gulp-clean');

// Configuration file to keep your code DRY
const { paths, browserSyncWatchFiles, browserSyncOptions } = require('./gulpconfig.json');
const { task, series, parallel, watch, dest, src } = gulp;

class CF_Compiler {

	constructor() {
		// Register tasks
		task('sass', this.compileSass);
		task('minifycss', this.minifyCss);
		task('imagemin', this.optimiseImages);
		task('watch', this.watchFiles);
		task('browser-sync', this.sync);
		task('scripts', this.compileScripts);
		task('copy-assets', this.copyAssets);
		task('fonts', this.copyFonts);
		task('clean', this.cleanDist);
		task('customiser', this.copyCustomiser);

		/**
		 * @run gulp imagemin-watch
		 * Starts watching images and reloads on changes
		 */
		task('imagemin-watch', series('imagemin', browserSync.reload));

		/**
		 * @run gulp styles
		 * Compiles and minifies
		 */
		task('styles', series('sass', 'minifycss'));

		/**
		 * @run gulp watch-bs
		 * Starts watcher with browser-sync. Browser-sync reloads page automatically on your browser
		 */
		task('watch-bs', parallel('browser-sync', 'watch', 'scripts', 'fonts', 'customiser'));

		/**
		 * @run gulp compile
		 * Compiles styles scripts and images
		 */
		task('compile', series('styles', 'scripts', 'imagemin', 'fonts', 'customiser'));
	}

	/**
	 * @run gulp watch
	 * Starts watcher. Watcher runs gulp sass task on changes
	 */
	watchFiles() {
		watch(`${paths.sass}/**/*.scss`, series('styles'));
		watch([`${paths.js}/**/*.js`, 'js/**/*.js', '!js/theme.js', '!js/theme.min.js'], series('scripts'));

		//Inside the watch task.
		watch(`${paths.img} /**`, series('imagemin-watch'));
	}

	/**
	 * @run gulp browser-sync
	 * Starts browser-sync task for starting the server.
	 */
	sync() {
		browserSync.init(browserSyncWatchFiles, browserSyncOptions);
	}

	/**
	 * @run gulp sass
	 * Compiles SCSS files in CSS
	 */
	compileSass() {
		const stream = src(`${paths.sass}/*.scss`)
			.pipe(plumber({
				errorHandler: function (err) {
					console.log(err);
					this.emit('end');
				}
			}))
			.pipe(sourcemaps.init({ loadMaps: true }))
			.pipe(sass({ errLogToConsole: true }))
			.pipe(postcss([autoprefixer()]))
			.pipe(sourcemaps.write(undefined, { sourceRoot: null }))
			.pipe(dest(paths.dist))
			.pipe(rename('custom-editor-style.css'));

		return stream;
	}

	/**
	 * @run gulp minifycss
	 * Minifies CSS files
	 */
	minifyCss() {
		return src([
			`${paths.dist}/custom-editor-style.css`,
			`${paths.dist}/theme.css`,
		])
			.pipe(sourcemaps.init({ loadMaps: true }))
			.pipe(cleanCSS({ compatibility: '*' }))
			.pipe(plumber({
				errorHandler: function (err) {
					console.log(err);
					this.emit('end');
				}
			}))
			.pipe(rename({ suffix: '.min' }))
			.pipe(sourcemaps.write('./'))
			.pipe(dest(paths.dist));
	}

	/**
	 * @run gulp scripts
	 * Compiles JS files
	 */
	compileScripts() {
		const scripts = [
			`${paths.js}/bootstrap4/bootstrap.bundle.js`, // All BS4 stuff
			`${paths.js}/themejs/*.js`, // All BS4 stuff
			`${paths.js}/skip-link-focus-fix.js`,
			// Adding currently empty javascript file to add on for your own themes´ customizations
			// Please add any customizations to this .js file only!
			`${paths.js}/custom-javascript.js`,
		];

		src(scripts, { allowEmpty: true })
			.pipe(babel({
				presets: ['@babel/preset-env'],
				plugins: ['@babel/plugin-transform-modules-commonjs']
			}))
			.pipe(concat('theme.min.js'))
			.pipe(uglify())
			.pipe(dest(paths.dist));

		return src(scripts, { allowEmpty: true })
			.pipe(babel())
			.pipe(concat('theme.js'))
			.pipe(dest(paths.dist));
	}

	/**
	 * @run gulp imagemin
	 * Running image optimizing task
	 */
	optimiseImages() {
		return src(`${paths.img}/**`)
			.pipe(
				imagemin(
					[
						// Bundled plugins
						imagemin.gifsicle({
							interlaced: true,
							optimizationLevel: 3,
						}),
						imagemin.mozjpeg({
							quality: 100,
							progressive: true,
						}),
						imagemin.optipng(),
						imagemin.svgo(),
					],
					{
						verbose: true,
					}
				)
			)
			.pipe(dest(paths.dist));
	}

	/**
	 * @run gulp copy-assets
	 * Copy all needed dependency assets files from bower_component assets to themes /js, /scss and /fonts folder. Run this task after bower install or bower update
	 */
	copyAssets() {

		////////////////// All Bootstrap 4 Assets /////////////////////////
		// Copy all JS files
		var stream = src(`${paths.node}bootstrap/dist/js/**/*.js`)
			.pipe(dest(`${paths.js}/bootstrap4`));

		// Copy all Bootstrap SCSS files
		src(`${paths.node}bootstrap/scss/**/*.scss`)
			.pipe(dest(`${paths.dev}/sass/vendor/bootstrap4`));

		////////////////// End Bootstrap 4 Assets /////////////////////////

		return stream;
	}

	/**
	 * @run gulp fonts
	 * Copy fonts to dist
	 */
	copyFonts() {
		const inputs = ['eot', 'svg', 'ttf', 'woff', 'woff2'].map(ext => `${paths.fonts}/**/*.${ext}`);
		return compiler._copy(inputs);
	}

	copyCustomiser() {
		return compiler._copy(`${paths.js}/customizer.js`)
	}

	cleanDist() {
		return src([
			`${paths.dist}/*`,
			`!${paths.dist}/.gitkeep`])
			.pipe(clean())
	}

	_copy(inputs, dist = null) {
		return src(inputs).pipe(dest(dist || paths.dist))
	}

}

const compiler = new CF_Compiler();
