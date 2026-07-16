<?php
/**
 * Register the Site Logo section.
 */

function register_site_logo( $wp_customize ) {
	//Second CTA Section
	add_section_image_field( $wp_customize, 'title_tagline', 'site_logo_image', 'Site Logo' );
}
