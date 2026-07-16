<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package SZ Starter Theme
 */

 function page_enqueue_scripts() {
	wp_enqueue_style( '404-styles' );
	//wp_enqueue_script( 'default-scripts' );
}
add_action( 'wp_enqueue_scripts', 'page_enqueue_scripts' );

get_header();
?>

	<main id="primary" class="site-main d-flex align-items-center">
		<div class="container">

			<div class="heading">
				<h1 class="page-title">Oops!🤖 <br/>Page not found.</h1>
			</div><!-- .heading -->

			<div class="content">
				<?php echo __( '<div><a class="btn" href="/">RETURN TO HOME</a></div>', 'sz-theme' ); ?>
			</div><!-- .content -->

		</div>
	</main><!-- #main -->

<?php
get_footer();
