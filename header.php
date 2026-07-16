<?php
/**
 * The template header
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package SZ Starter Theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="referrer" content="no-referrer-when-downgrade">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="address=no">
<meta name="theme-color" content="#ffffff">
<meta name="msapplication-TileColor" content="#ffffff">
<?php get_template_part('template-parts/general/head-fonts'); ?>

<style><?= file_get_contents(__DIR__ . '/dist/css/critical.css'); ?></style>
<link rel="icon" type="image/png" sizes="16x16" href="<?= get_stylesheet_directory_uri(); ?>/favicon/favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="<?= get_stylesheet_directory_uri(); ?>/favicon/favicon-32x32.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?= get_stylesheet_directory_uri(); ?>/favicon/apple-touch-icon.png">
<?php wp_head(); ?>

<?php if (function_exists('get_field')) { echo get_field('cookiebot_code', 'option') ?? ''; $gtm_id = get_field('gtm_id', 'option'); echo !empty($gtm_id) ? '<script>window.GTM_ID="' . $gtm_id . '";</script>' : ''; } ?>

</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>
	<?= function_exists('get_field') ? (get_field('gtm_body_code', 'option') ?? '') : ''; ?>

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'sz-theme' ); ?></a>

		<?php get_template_part('template-parts/layout/site-header'); ?>
