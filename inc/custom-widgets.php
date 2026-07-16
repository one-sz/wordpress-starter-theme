<?php
require get_stylesheet_directory() . '/inc/custom-widgets/social-links.php';
require get_stylesheet_directory() . '/inc/custom-widgets/footer-widgets.php';

function sz_theme_child_widgets_init() {
	register_widget( 'Custom_Social_Links' );
}
add_action( 'widgets_init', 'sz_theme_child_widgets_init' );