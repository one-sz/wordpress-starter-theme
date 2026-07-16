<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package SZ Starter Theme
 */

 get_header();
 ?>

	 <main id="primary" class="site-main container">

		 <?php while ( have_posts() ) {
			the_post(); the_content();
			 if (the_content() === null) { echo '<h1 class="d-flex align-items-center justify-content-center text-center" style="height: 50vh;">No template built.</h1>'; }
		} ?>

	 </main><!-- #main -->

 <?php
// get_sidebar();
 get_footer();