<?php
/**
* Configuration array for Custom Post Types and their taxonomies.
* To add a new CPT, just add another element to this array.
*/
$custom_post_types_config = [
	'solutions' => [
		'plural' => 'Solutions',
		'singular' => 'Solution',
		'slug' => 'solution',
		'hierarchical' => true,
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'author'],
		'taxonomies' => [
			[
				'singular' => 'Category',
				'plural' => 'Categories',
				'name' => 'solution-category',
				'slug' => 'solutions/category',
				'publicly_queryable' => false,
			],
			[
				'singular' => 'Type',
				'plural' => 'Types',
				'name' => 'solution-type',
				'slug' => 'solutions/type',
				'publicly_queryable' => false,
			],
		],
	],
];


//Register Custom Post Types and Taxonomies.
foreach ($custom_post_types_config as $post_type => $settings) {

	// CPT settings.
	$is_hierarchical = $settings['hierarchical'] ?? false;
	$supports = $settings['supports'] ?? [ 'title', 'editor', 'excerpt', 'thumbnail', ];
	// Add Parent dropdown support for hierarchical CPTs.
	if ($is_hierarchical) { $supports[] = 'page-attributes'; }

	// Register Custom Post Type.
	register_post_type($post_type, [
		'labels' => [
			'name' => $settings['plural'],
			'singular_name' => $settings['singular'],
			'all_items' => 'All ' . $settings['plural'],
			'view_item' => 'View ' . $settings['singular'],
			'add_new_item' => 'Add ' . $settings['singular'],
			'add_new' => 'Add ' . $settings['singular'],
			'edit_item' => 'Edit ' . $settings['singular'],
		],
		'hierarchical' => $is_hierarchical,
		'has_archive' => false,
		'public' => true,
		'show_ui' => true,
		'menu_position' => 20,
		'rewrite' => [ 'slug' => $settings['slug'] ?? $post_type, ],
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-open-folder',
		'supports' => array_unique($supports),
	]);


	//Register Taxonomies.
	if (!empty($settings['taxonomies'])) {
		foreach ($settings['taxonomies'] as $taxonomy) {
			$taxonomy_name = $taxonomy['name'];
			$publicly_queryable = $taxonomy['publicly_queryable'] ?? true;
			$rewrite = ( $publicly_queryable && isset($taxonomy['slug']) ) ? ['slug' => $taxonomy['slug']] : false;
			register_taxonomy($taxonomy_name, $post_type, [
				'labels' => [
					'name' => $taxonomy['plural'],
					'singular_name' => $taxonomy['singular'],
					'all_items' => 'All ' . $taxonomy['plural'],
					'edit_item' => 'Edit ' . $taxonomy['singular'],
					'view_item' => 'View ' . $taxonomy['singular'],
					'add_new_item' => 'Add ' . $taxonomy['singular'],
					'new_item_name' => 'New ' . $taxonomy['singular'],
					'search_items' => 'Search ' . $taxonomy['plural'],
				],
				'hierarchical' => true,
				'public' => true,
				'publicly_queryable' => $publicly_queryable,
				'rewrite' => $rewrite,
				'show_in_rest' => true,
				'show_admin_column' => true,
				'has_archive' => false,
			]);
		}
	}


	// Add Author column to CPT admin list.
	add_filter("manage_{$post_type}_posts_columns", function ($columns) {
		$new_columns = [];
		foreach ($columns as $key => $value) {
			$new_columns[$key] = $value;
			if ($key === 'title') { $new_columns['author'] = 'Author'; }
		}
		return $new_columns;
	});
}