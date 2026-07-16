<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package SZ Starter Theme
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) { ?>


			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>


			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				//get_template_part( 'template-parts/parts/post-item', get_post_type() );

			endwhile;

			the_posts_navigation();

		} ?>

	</main><!-- #main -->

<?php
get_footer();
