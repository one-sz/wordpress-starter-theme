<?php
/**
 * Register the social links section.
 */
function register_social_links( $wp_customize ) {
	$section_id = 'social_links';

	$wp_customize->add_section( $section_id , array(
		'title' => __( 'Social Links' ),
		'priority' => 30
	) );

	$social_links = get_social_links_array();
	foreach ( $social_links as $social_link ) {
		add_section_link_field( $wp_customize, $section_id, $social_link . '_link', ucfirst( $social_link ) . ' Link' );
	}
}

/**
 * Get array containing the types of social links.
 */
function get_social_links_array() {
	$social_links = array(
		'twitter',
		'linkedin',
		'instagram',
		'facebook',
	);

	return $social_links;
}