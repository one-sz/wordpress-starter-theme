<?php
/**
 * Theme functions and definitions
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package SZ Starter Theme
*/

if ( ! defined( 'ABSPATH' ) ) { header("Location: /404/", true, 301); exit; }


//Custom Login Page
locate_template( 'my-admin/login.php', true, true );


/**
 * Global enqueues
 * @since 1.0.0
 * @global array $wp_styles
*/

function sz_theme_global_enqueues() {

	//wp_dequeue_style('child-theme'); //activate only if use as child theme

	// jQuery
	if( ! is_admin() ) {
		wp_deregister_script( 'jquery' );
		//wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.min.js' ), false, NULL, true ); //jQuery WITHOUT defer attribute if something doesn't work
		wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.min.js' ), array(), null, ['strategy' => 'defer', 'in_footer' => true] );
		wp_enqueue_script( 'jquery' );
	}

	//wp_enqueue_script('general-scripts', get_template_directory_uri() . '/dist/js/general.js', '', filemtime(get_template_directory() . '/dist/js/general.js'), true); //WITHOUT defer attribute if something doesn't work
	wp_enqueue_script( 'general-scripts', get_template_directory_uri() . '/dist/js/general.js', array(), filemtime(get_template_directory() . '/dist/js/general.js'), ['strategy' => 'defer', 'in_footer' => true] );
	wp_enqueue_script( 'analytics-scripts', get_template_directory_uri() . '/dist/js/analytics.js', array(), filemtime(get_template_directory() . '/dist/js/analytics.js'), ['strategy' => 'defer', 'in_footer' => true] );
	wp_enqueue_style('general-styles', get_template_directory_uri() . '/dist/css/general.css', array(), filemtime(get_template_directory() . '/dist/css/general.css'));

	//Pages
	wp_register_style('404-styles', get_stylesheet_directory_uri() . '/dist/css/pages/404.css', array(), filemtime(get_stylesheet_directory() . '/dist/css/pages/404.css'));

	// Preload CSS and asynchronous
	add_filter('style_loader_tag', function($html, $handle, $href, $media) {
		$async_handles = [
			'general-styles',
			'404-styles',
			//Gravity forms - uncomment if Gravity Forms is used
			//'gravity_forms_theme_reset',
			//'gravity_forms_theme_foundation',
			//'gravity_forms_theme_framework',
			//'gravity_forms_orbital_theme',
			//Other plugins (without -css)
			//'style-id'
		];
		if (in_array($handle, $async_handles)) {
			return "<link rel='preload' href='{$href}' as='style' onload=\"this.onload=null;this.rel='stylesheet'\" media='{$media}'>"."\n<noscript><link rel='stylesheet' href='{$href}' media='{$media}'></noscript>\n";
		}
		return $html;
	}, 10, 4);


	// Defer JS (wp, theme and plugins)
	//foreach (['wp-dom-ready','wp-hooks','wp-i18n','wp-a11y',] as $handle) { wp_script_add_data($handle, 'strategy', 'defer'); }


	//Register the blocks assets
	$post = get_post();
	if ( !empty($post) ) {
		if (has_blocks($post->post_content)) {
			$blocks = parse_blocks($post->post_content);
			register_blocks_assets($blocks);
		}
	}

	wp_localize_script( 'general-scripts', 'ajax_params', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );

}
add_action( 'wp_enqueue_scripts', 'sz_theme_global_enqueues', 10 );

/* START BLOCKS (also ACF blocks) related */
function register_blocks_assets( array $blocks ): void {

	static $checked_files = []; //Cache for file_exists() and filemtime()
	static $processed = []; //Avoids processing the same block multiple times

	if ( empty( $blocks ) ) { return; }

	foreach ( $blocks as $block ) {
		$block_name = $block['blockName'] ?? '';
		if ( empty( $block_name ) || ! str_contains( $block_name, '/' ) ) { continue; }
		[ $type, $name ] = explode( '/', $block_name, 2 );

		// Recurse early for non-ACF blocks
		if ( $type !== 'acf' ) {
			if ( ! empty( $block['innerBlocks'] ) ) { register_blocks_assets( $block['innerBlocks'] ); }
			continue;
		}

		// If the block has already been processed during this request, we skip it
		if ( isset( $processed[ $name ] ) ) { continue; }
		$processed[ $name ] = true;
		$base_dir = get_template_directory() . '/blocks/' . $name;
		$base_uri = get_template_directory_uri() . '/blocks/' . $name;
		$style_handle = $name . '-styles';
		$script_handle = $name . '-scripts';

		//CSS
		if ( ! isset( $checked_files[ $name ]['css'] ) ) { $css_path = $base_dir . '/block-style.css'; $checked_files[ $name ]['css'] = file_exists( $css_path ) ? filemtime( $css_path ) : false; }
		if ( $checked_files[ $name ]['css'] !== false && ! wp_style_is( $style_handle, 'enqueued' ) ) {
			wp_enqueue_style( $style_handle, $base_uri . '/block-style.css', [], $checked_files[ $name ]['css'] );
		}

		//JS
		if ( ! isset( $checked_files[ $name ]['js'] ) ) { $js_path = $base_dir . '/block-script.js'; $checked_files[ $name ]['js'] = file_exists( $js_path ) ? filemtime( $js_path ) : false; }
		if ( $checked_files[ $name ]['js'] !== false && ! wp_script_is( $script_handle, 'enqueued' ) ) {
			wp_enqueue_script( $script_handle, $base_uri . '/block-script.js', ['jquery'], $checked_files[ $name ]['js'], [ 'strategy' => 'defer', 'in_footer' => true, ] );
		}

		// Recurse for inner blocks
		if ( ! empty( $block['innerBlocks'] ) ) { register_blocks_assets( $block['innerBlocks'] ); }
	}
}

//CRITICAL for VITE compilation
add_filter( 'script_loader_tag', function( $tag, $handle ) {
	if ( strpos( $handle, '-scripts' ) !== false ) { return str_replace( '<script', '<script type="module"', $tag ); }
	return $tag;
}, 10, 2 );

// Global async CSS loader
add_filter( 'style_loader_tag', function( $html, $handle, $href, $media ) {
	if ( str_ends_with( $handle, '-styles' ) ) {
		return "<link rel='preload' href='{$href}' as='style' onload=\"this.onload=null;this.rel='stylesheet'\" media='{$media}'>"."\n<noscript><link rel='stylesheet' href='{$href}' media='{$media}'></noscript>\n";
	}
	return $html;
}, 10, 4 );

//Save ACF JSON as files
add_filter( 'acf/settings/save_json', function() { return get_stylesheet_directory() . '/acf-json'; });
/* END BLOCKS related */


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
*/
function sz_theme_setup() {
	// Includes
	$includes = [
		'wordpress-cleanup.php',
		'tinymce.php',
		'helper-functions.php',
		'customizer.php',
		//'custom-sidebars.php',
		'custom-widgets.php',
		'custom-post-types.php',
		'body-class.php',
		'ajax-calls.php',
		'acf-blocks.php',
	];
	foreach ( $includes as $file ) { include_once get_stylesheet_directory() . "/inc/$file"; }

	// Image Sizes
	// add_image_size( 'sz_featured', 400, 100, true );

	// Gutenberg
	// -- Responsive embeds
	add_theme_support( 'responsive-embeds' );

	// -- Wide Images
	add_theme_support( 'align-wide' );

	// -- Disable custom font sizes
	add_theme_support( 'disable-custom-font-sizes' );

	// -- Editor Font Styles
	add_theme_support( 'editor-font-sizes', [
		[ 'name' => __( 'small', 'sz_theme' ), 'shortName' => __( 'S', 'sz_theme' ), 'size' => 12,'slug' => 'small' ],
		[ 'name' => __( 'regular', 'sz_theme' ), 'shortName' => __( 'M', 'sz_theme' ), 'size' => 16, 'slug' => 'regular' ],
		[ 'name' => __( 'large', 'sz_theme' ), 'shortName' => __( 'L', 'sz_theme' ), 'size' => 20, 'slug' => 'large' ],
	] );

	// -- Disable Custom Colors
	add_theme_support( 'disable-custom-colors' );

	// -- Editor Color Palette
	add_theme_support( 'editor-color-palette', [
		[ 'name' => __( 'Blue', 'sz_theme' ), 'slug' => 'blue', 'color' => '#59BACC', ],
		[ 'name' => __( 'Green', 'sz_theme' ), 'slug' => 'green', 'color' => '#58AD69', ],
		[ 'name' => __( 'Orange', 'sz_theme' ), 'slug' => 'orange', 'color' => '#FFBC49', ],
		[ 'name' => __( 'Red', 'sz_theme' ), 'slug' => 'red', 'color' => '#E2574C', ],
	] );

	/*
	* Make theme available for translation.
	* Translations can be filed in the /languages/ directory.
	*/
	load_theme_textdomain( 'sz-theme', get_template_directory() . '/languages' );

	/*
	* Let WordPress manage the document title.
	* By adding theme support, we declare that this theme does not use a
	* hard-coded <title> tag in the document head, and expect WordPress to
	* provide it for us.
	*/
	add_theme_support( 'title-tag' );

	/*
	* Enable support for Post Thumbnails on posts and pages.
	*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus([
		'primary' => esc_html__( 'Main Menu', 'sz-theme' ),
		'secondary' => esc_html__( 'Footer Menu', 'sz-theme' ),
	]);

	/*
	* Switch default core markup for search form, comment form, and comments
	* to output valid HTML5.
	*/
	add_theme_support(
		'html5',
		['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',]
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	//Remove margin-top from admin-bar on tablet and mobile
	add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );

	// Add "Registered" column in Users menu in backend
	add_filter( 'manage_users_columns', fn( $columns ) => $columns + [ 'registered' => 'Registered' ] );
	add_filter( 'manage_users_custom_column', function( $value, $column, $user_id ) {
		if ( $column === 'registered' ) { $user = get_userdata( $user_id );  return mysql2date( 'd.m.Y H:i', $user->user_registered ); } return $value;
	}, 10, 3 );
}
add_action( 'after_setup_theme', 'sz_theme_setup' );


add_action( 'init', function() {
	// Remove Tags from default posts
	register_taxonomy( 'post_tag', [] );
	/** Disable the emoji's */
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );

	// Remove unwanted SVG filter injection WP
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

	//Allow images up to 2880px (2K) wide before scaling in Media Library
	add_filter( 'big_image_size_threshold', function() { return 2880; });

	// Disable various features (useless most of them)
	remove_action('wp_head', 'wp_generator'); //Wordpress Version Generator
	remove_action('wp_head', 'wlwmanifest_link'); //Windows Live Writer
	remove_action('wp_head', 'rsd_link'); //Really Simple Discovery
	remove_action('wp_head', 'wp_shortlink_wp_head'); //Remove shortlink from <head>
	remove_action( 'template_redirect', 'wp_shortlink_header', 11 );//Remove shortlink from "Response Headers"
	remove_action('wp_head', 'wp_oembed_add_discovery_links'); //oEmbed
	//remove_action('wp_head', 'rest_output_link_wp_head'); //REST API link (should NOT remove it, used in many plugins)
	//remove_action('template_redirect', 'rest_output_link_header', 11); //REST API header

	//add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' ); // Disables the block editor from managing widgets in the Gutenberg plugin.

	add_filter('use_widgets_block_editor', '__return_false'); //Enable the old widgets style in Appearance

	if ( is_admin() ) { add_action( 'admin_head', function() { echo '<link rel="icon" type="image/x-icon" sizes="16x16" href="' . esc_url( get_stylesheet_directory_uri() . '/favicon/favicon.ico' ) . '" />'; }); };//Favicon for backend only
});


/** Filter function used to remove the tinymce emoji plugin */
function disable_emojis_tinymce( $plugins ) { return array_diff( (array) $plugins, ['wpemoji'] ); }


/** Remove emoji CDN hostname from DNS prefetching hints */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) { return 'dns-prefetch' === $relation_type ? array_diff( $urls, [ apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' ) ] ) : $urls; }


/** START - REMOVE COMMENTS SUPPORT*/
add_action('admin_menu', fn() => remove_menu_page('edit-comments.php')); //From Admin Menu
add_action('wp_before_admin_bar_render', fn() => $GLOBALS['wp_admin_bar']->remove_menu('comments')); //From Admin Bar
add_action('admin_init', function() { //Redirect any user trying to access comments page
	global $pagenow;
	if ($pagenow === 'edit-comments.php') { wp_safe_redirect(admin_url()); exit; }
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	foreach (get_post_types() as $pt) {
		if (post_type_supports($pt, 'comments')) {
			remove_post_type_support($pt, 'comments');
			remove_post_type_support($pt, 'trackbacks');
		}
	}
});
// Close on the Frontend
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
// Remove ONLY comments feed link from head
add_action('wp_head', function() {
	remove_action('wp_head', 'feed_links', 2);
	// Add back only the main posts feed
	echo '<link rel="alternate" type="application/rss+xml" title="'. esc_attr(get_bloginfo('name')) . ' &raquo; Feed" href="'. esc_url(get_feed_link()). "\" />\n";
}, 1);
remove_action('wp_head', 'feed_links_extra', 3); // Keep extra feeds (categories, tags, etc.) but remove comments
add_action('template_redirect', function() { if (is_comment_feed()) { wp_redirect(home_url(), 301); exit; } }); // 301 redirect for comments feed to homepage
/** END - REMOVE COMMENTS */


//Set page slug as class on BODY
/* add_filter( 'body_class', function( $classes ) {
	$id = get_the_ID();
	// Add "white-header" class if field "meta _header_color" = on
	if ( get_post_meta( $id, '_header_color', true ) === 'on' ) { $classes[] = 'white-header'; }
	// Add slug of the page
	if ( is_page() ) { $classes[] = get_post_field( 'post_name', $id ) . '-page'; }
	return $classes;
}); */


//Change Gravity Forms default ajax spinner
/* add_filter( 'gform_ajax_spinner_url', 'spinner_url', 10, 2 );
function spinner_url( $image_src, $form ) { return get_template_directory_uri() . '/dist/images/svg/ajax-loader.svg'; }*/

/* add_action( 'gform_register_init_scripts', 'gform_body_class_submit_state' ); //used to add a class on body for the submiting event
function gform_body_class_submit_state( $form ) {
	$script = "
	(function($){

		$('#gform_' + {$form['id']}).on('submit', function(){
			$('body').addClass('gf-submitting');
		});

		$(document).on('gform_post_render', function(e, formId){
			if (formId == {$form['id']}) {
				if ($('#gform_' + formId + ' .gfield_error').length) {
					$('body').removeClass('gf-submitting');
				}
			}
		});

		$(document).on('gform_confirmation_loaded', function(e, formId){
			if (formId == {$form['id']}) {
				$('body').removeClass('gf-submitting');
			}
		});

	})(jQuery);
	";

	GFFormDisplay::add_init_script(
		$form['id'],
		'body_class_submit_state',
		GFFormDisplay::ON_PAGE_RENDER,
		$script
	);
} */

//add_filter( 'gform_disable_css', '__return_true' );

//add_filter( 'gform_confirmation_anchor', '__return_false' );//disable page scroll after submision

//Needed if <select> fields have custom CSS library
/* add_action( 'gform_register_init_scripts', 'gform_tom_select_trigger' );
function gform_tom_select_trigger( $form ) {

	$script = "(function($){
		const event = new Event('formRendered');
		window.dispatchEvent(event);
	})(jQuery);";

	GFFormDisplay::add_init_script( $form['id'], 'tom_select_trigger', GFFormDisplay::ON_PAGE_RENDER, $script );
} */
//Gravity to add a class on body if form is sent
/* add_action( 'gform_register_init_scripts', 'gform_add_body_class_on_success' );
function gform_add_body_class_on_success( $form ) {

	$script = <<<EOD
	(function($) {
		$(document).on('gform_confirmation_loaded', function(event, formId){
			if (formId === {$form['id']}) {
				$('body').addClass('QQQQQ');
			}
		});
	})(jQuery);
	EOD;

	GFFormDisplay::add_init_script( $form['id'], 'add_body_class_on_success', GFFormDisplay::ON_PAGE_RENDER, $script );
}*/


//Yoast disable SearchAction in schema
//add_filter( 'disable_wpseo_json_ld_search', '__return_true' );

// Disable and return 404 for search page in frontend, but allow search in admin
/* add_action( 'parse_query', function( $query ) {
	if ( !is_admin() && $query->is_search() ) {
		$query->is_search  = false;
		$query->query_vars['s'] = '';
		$query->is_404 = true;
		status_header(404);
	}
}); */


/** Gutenberg scripts and styles */
add_action( 'enqueue_block_assets', function() {
	if ( ! is_admin() ) { return; }
	$uri = get_template_directory_uri() . '/dist';
	$dir = get_template_directory();
	wp_enqueue_style( 'editor-styles', "$uri/css/editor-styles.css", [], filemtime( "$dir/dist/css/editor-styles.css" ) );//Style used for blocks preview in backend
	//wp_enqueue_script( 'editor-scripts', "$uri/js/editor.js", [ 'wp-blocks', 'wp-dom' ], filemtime( "$dir/dist/js/editor.js" ), true );
}, 20 );

// Don't load Gutenberg-related stylesheets.
/* add_action( 'wp_enqueue_scripts', function() {
	// Dequeue block styles
	foreach ( [ 'wp-block-library', 'wp-block-library-theme', 'wc-block-style', 'storefront-gutenberg-blocks' ] as $handle ) { wp_dequeue_style( $handle ); }
	// Remove global styles actions
	foreach ( [ ['wp_enqueue_scripts', 'wp_enqueue_global_styles'], ['wp_footer', 'wp_enqueue_global_styles', 1] ] as $args ) {
		remove_action( ...$args );
	}
}, 100 ); */


//Disable polyfill JS for old browsers (speed increase, but on older browsers some features may not work)
add_action( 'wp_enqueue_scripts', function() { foreach ( [ 'wp-polyfill', 'regenerator-runtime' ] as $handle ) { wp_deregister_script( $handle ); } });


//WCAG for Header and Footer Menus
add_filter('nav_menu_link_attributes', function ($a,$i,$g){
	// aria-label
	if(in_array($g->theme_location,['primary','secondary'])||!$g->theme_location)
		$a['aria-label']=$i->attr_title?:$i->title;
	// aria-haspopup if has submenu
	if(in_array('menu-item-has-children',$i->classes))
		$a+=['aria-haspopup'=>'true','aria-expanded'=>'false'];
	return $a;
},10,3);


//Add current year in footer automatically for (c) with [year] shortcode in a Text widget
//add_shortcode('year', fn() => wp_date('Y'));

//Clear HTML on server before is sent to browser
add_action( 'template_redirect', function() {
	if ( is_admin() ) return; //Frontend only
	ob_start(function( $buffer ) {
		$buffer = preg_replace('/^\s+/m', '', $buffer); //Remove the spaces/tabs at the beginning of each line
		$buffer = preg_replace('/^\s*$/m', '', $buffer); //Optional: remove empty lines
		return $buffer;
	});
});