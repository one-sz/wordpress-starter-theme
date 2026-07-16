<?php
/**
 * Register the copyright section.
 */
function register_copyright( $wp_customize ) {
    $section_id = 'copyright';

    $wp_customize->add_section( $section_id , array(
        'title'    => __( 'Copyright', 'sz-theme' ),
        'priority' => 30
    ) );

    add_section_text_field( $wp_customize, $section_id, 'copyright_text', 'Copyright Text' );
}
