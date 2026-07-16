<?php
function sz_footer_widgets_init() {
	// First footer widget area, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget 1', 'sz_theme' ),
		'id' => 'footer-widget-1',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Second Footer Widget Area, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget 2', 'sz_theme' ),
		'id' => 'footer-widget-2',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Third Footer Widget Area, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget 3', 'sz_theme' ),
		'id' => 'footer-widget-3',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Forth Footer Widget Area, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget 4', 'sz_theme' ),
		'id' => 'footer-widget-4',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

}

// Register sidebars by running hylandhill_widgets_init() on the widgets_init hook.
add_action( 'widgets_init', 'sz_footer_widgets_init' );