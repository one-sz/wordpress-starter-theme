<?php
/**
 * The main template file
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package SZ Starter Theme
 */

get_header();
?>

	<main id="primary" class="site-main">
		<?php if ( have_posts() ) { if ( is_home() && ! is_front_page() ) { ?>
			<header><h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1></header>
		<?php } while ( have_posts() ) { the_post(); the_content(); } } ?>
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();