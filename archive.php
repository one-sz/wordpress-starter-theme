<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package OS Theme
 */

 get_header();
 ?>

	<main id="primary" class="site-main">
		<?php if ( have_posts() ) {

			the_archive_title( '<h1 class="page-title text-center">', '</h1>' );

			while ( have_posts() ) {
				the_post();
				$content = get_the_content();
				if ( ! empty( trim( $content ) ) ) {
					the_content();
				} else {
					echo '<h1 class="d-flex align-items-center justify-content-center text-center" style="height: 50vh;">No template built.</h1>';
				}

				the_posts_navigation();
			}
		} ?>
	</main><!-- #main -->

 <?php
// get_sidebar();
 get_footer();