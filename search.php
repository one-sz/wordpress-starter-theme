<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package SZ Starter Theme
 */
/* function page_enqueue_scripts() {
	wp_enqueue_style( 'search-styles' );
	//wp_enqueue_script( 'search-scripts' );
}
add_action( 'wp_enqueue_scripts', 'page_enqueue_scripts' ); */

get_header();
?>

<style><?//= file_get_contents(__DIR__ . '/dist/css/pages/search.css'); ?></style>

	<main id="primary" class="site-main">

		<div class="container">

			<?php if ( have_posts() ) { ?>

				<h1 class="page-title"><?php printf( '<span class="text-uppercase">' . get_search_query() . ' Results</span>' ); ?></h1>

			<?php while ( have_posts() ) { the_post(); ?>

				<h3 class="item-title"><a href="<?= esc_url( get_permalink() ); ?>"><?= esc_html( get_the_title() ); ?></a></h3>
				<div class="item-excerpt">
					<?php $content = get_the_excerpt() ?: wp_strip_all_tags( apply_filters('the_content', strip_shortcodes(get_the_content())) ); echo esc_html(wp_trim_words($content, 50)); ?>
				</div>

			<?php }

				// Get pagination variables
				$paged = max(1, get_query_var('paged'));
				$total = $wp_query->max_num_pages;
				$links = '';
				if ($paged > 1) { $links .= '<a class="first page-numbers" href="' . get_pagenum_link(1) . '">« First</a>'; }
				$links .= paginate_links([ 'total' => $total, 'current' => $paged, 'mid_size' => 2, 'prev_text' => '‹', 'next_text' => '›', 'echo' => false, ]);
				if ($paged < $total) { $links .= '<a class="last page-numbers" href="' . get_pagenum_link($total) . '">Last »</a>'; }
				echo $links;

			} else { ?>

				<h2 class="no-results">NO RESULTS FOUND</h2>

			<?php } ?>

		</div>

	</main><!-- #main -->

<?php
get_footer();