import { defineConfig } from 'vite';
import { resolve } from 'path';
import { readdirSync, statSync } from 'fs';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import autoprefixer from 'autoprefixer';

const __dirname = resolve();

// Helper function to get files from a directory
function getFiles(dir) { return readdirSync(dir).filter((file) => statSync(resolve(dir, file)).isFile()); }


// Build entries for blocks dynamically
function buildBlockEntries() {
	const entries = {};

	// SCSS blocks - go to blocks/{name}/block.css
	getFiles('src/scss/blocks').forEach((file) => {
		if (file !== 'index.scss' && file !== 'hero.scss') {
			const name = file.split('.')[0];
			entries[`blocks/${name}/block-style`] = resolve(__dirname, `src/scss/blocks/${file}`);
		}
	});

	// JS blocks - go to blocks/{name}/block.js
	getFiles('src/js/blocks').forEach((file) => {
		const name = file.split('.')[0];
		entries[`blocks/${name}/block-script`] = resolve(__dirname, `src/js/blocks/${file}`);
	});

	return entries;
}


export default defineConfig(({ mode }) => {
	const isProduction = mode === 'production';

	return {
		base: './',
		publicDir: false,

		define: {
			$: 'window.jQuery',
			jQuery: 'window.jQuery',
		},

		build: {
			manifest: isProduction,
			outDir: resolve(__dirname, '.'), // Output in root
			emptyOutDir: false,
			sourcemap: !isProduction,
			minify: isProduction ? 'terser' : false,

			rollupOptions: {
				input: {
					// Critical CSS → dist/css/
					'dist/css/critical': resolve(__dirname, 'src/scss/critical.scss'),
					'dist/css/editor-styles': resolve(__dirname, 'src/scss/editor-styles.scss'),
					'dist/css/general': resolve(__dirname, 'src/scss/general.scss'),

					// General → dist/css/ și dist/js/
					//'dist/js/editor': resolve(__dirname, 'src/js/editor.js'),
					'dist/js/general': resolve(__dirname, 'src/js/general.js'),
					'dist/js/analytics': resolve(__dirname, 'src/js/analytics.js'),

					// Pages → dist/css/pages/
					'dist/css/pages/404': resolve(__dirname, 'src/scss/pages/404.scss'),

					// Blocks → blocks/{name}/
					...buildBlockEntries(),
				},
				output: {
					assetFileNames: (assetInfo) => {
						if (assetInfo.name.endsWith('.css')) {
							return '[name][extname]';
						}
						return 'assets/[name]-[hash][extname]';
					},
					chunkFileNames: 'dist/js/chunks/[name].js',
					entryFileNames: (chunkInfo) => {
						// Blocks go to blocks/{name}/block.js
						if (chunkInfo.name.startsWith('blocks/')) {
							return '[name].js';
						}
						// Everything else goes to dist/
						return '[name].js';
					},
				},
			},
			terserOptions: isProduction ? { compress: { drop_console: false } } : undefined,
		},

		css: {
			devSourcemap: !isProduction,
			preprocessorOptions: {
				scss: {
					loadPaths: [resolve(__dirname, 'node_modules')],
					api: 'modern-compiler',
					silenceDeprecations: ['import', 'color-functions', 'global-builtin', 'if-function'],
					//quietDeps: true,
					/* logger: { warn() {}, // ignore warnings }, */
					importers: [
						{
							findFileUrl(url) {
								if (url.startsWith('~')) {
									return new URL(`file:///${resolve(__dirname, 'node_modules', url.slice(1)).replace(/\\/g, '/')}`);
								}
									return null;
							},
						},
					],
				},
			},
			postcss: {
				plugins: [
					autoprefixer({
						overrideBrowserslist: [ //Exclude -ms- prefix (IE/old Edge)
							'last 2 versions',
							'> 1%',
							'not dead',
							'not IE 11',
							'not IE_Mob 11',
						],
					}),
				],
			},
		},

		resolve: {
			alias: {
				'~': resolve(__dirname, 'node_modules'),
				'@': resolve(__dirname, 'src'),
			},
		},

		plugins: [
			// Copy images directory
			viteStaticCopy({
				targets: [
					{
						src: 'src/images/',
						dest: 'dist/',
					},
				],
			}),
		],

		server: {
			hmr: false,
			watch: { usePolling: false },
		},
	};

})