<?php
/**
 * Theme Customizer
 */

require get_stylesheet_directory() . '/inc/customizer/site-logo.php';
require get_stylesheet_directory() . '/inc/customizer/social-links.php';


/**
 * Add support for custom options in the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function sz_theme_customize_register( $wp_customize ) {
    register_site_logo( $wp_customize );
    register_social_links( $wp_customize );
}
add_action( 'customize_register', 'sz_theme_customize_register' );


/**
 * Add text field to customizer section.
 */
function add_section_text_field( $wp_customize, $section_id, $field_id, $field_label ) {
    $wp_customize->add_setting( $field_id , array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'default'    => '',
    ) );

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, $field_id, array(
        'label'   => __( $field_label, 'sz-theme' ),
        'section' => $section_id,
    ) ) );
}

/**
 * Add link field to customizer section.
 */
function add_section_link_field( $wp_customize, $section_id, $field_id, $field_label ) {
    $wp_customize->add_setting( $field_id , array(
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ) );

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, $field_id, array(
        'label'   => __( $field_label, 'sz-theme' ),
        'section' => $section_id,
    ) ) );
}

/**
 * Add image field to customizer section.
 */
function add_section_image_field( $wp_customize, $section_id, $field_id, $field_label ) {
    $wp_customize->add_setting( $field_id , array(
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $field_id, array(
        'label'   => __( $field_label, 'sz-theme' ),
        'section' => $section_id,
    ) ) );
}


/**
 * Add textarea field to customizer section.
 */
function add_section_textarea_field( $wp_customize, $section_id, $field_id, $field_label ) {
    $wp_customize->add_setting( $field_id , array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'default'    => '',
    ) );

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, $field_id, array(
        'type'     => 'textarea',
        'label'    => __( $field_label, 'sz-theme' ),
        'section'  => $section_id,
    ) ) );
}

/**
 * Add checkbox field to customizer section.
 */
function add_section_checkbox_field( $wp_customize, $section_id, $field_id, $field_label ) {
    $wp_customize->add_setting( $field_id , array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'default'    => '',
    ) );

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, $field_id, array(
        'type'    => 'checkbox',
        'label'   => __( $field_label, 'sz-theme' ),
        'section' => $section_id,
    ) ) );
}

/**
 * Add Color Picker field to customizer section.
 */
function add_section_color_field( $wp_customize, $section_id, $field_id, $field_label ) {
    $wp_customize->add_setting( $field_id , array(
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
        'default'    => '',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $field_id, array(
        'label'   => __( $field_label, 'sz-theme' ),
        'section' => $section_id,
    ) ) );
}

/**
 * Add select field to customizer section.
 */
function add_section_select_field( $wp_customize, $section_id, $field_id, $field_label, $options ) {
    $wp_customize->add_setting( $field_id , array(
        'type'          => 'theme_mod',
        'capability'    => 'edit_theme_options',
        'default'       => '',
    ) );

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, $field_id, array(
        'type'      => 'select',
        'label'     => __( $field_label, 'sz-theme' ),
        'section'   => $section_id,
        'choices'   => $options
    ) ) );
}
