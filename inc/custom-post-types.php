<?php

/**
 * Configuration array for Custom Post Types and their taxonomies.
 * To add a new CPT, just add another element to this array.
 */
$custom_post_types_config = [
	'services' => [
		'plural' => 'Services',
		'singular' => 'Service',
		'slug' => 'service', // Custom CPT rewrite slug
		'hierarchical' => true, // Enable parent-child relationship for this CPT
		'menu_icon' => 'dashicons-buddicons-buddypress-logo', // Custom dashicon
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'author'], // Custom supports
		'taxonomies' => [
			[
				'singular' => 'Category',
				'plural' => 'Categories',
				'name' => 'service-category',
				'slug' => 'services/category',
				'publicly_queryable' => false, // Disable public links/archives for this taxonomy
			],
			[
				'singular' => 'Type',
				'plural' => 'Types',
				'name' => 'service-type',
				'slug' => 'services/type',
				'publicly_queryable' => false, // Disable public links/archives for this taxonomy
			],
		],
	],
];

	'solutions' => [
		'plural' => 'Solutions',
		'singular' => 'Solution',
		'slug' => 'solution', // Custom CPT rewrite slug
		'hierarchical' => true, // Enable parent-child relationship for this CPT
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'author'], // Custom supports
		'taxonomies' => [
			[
				'singular' => 'Solution Category',
				'plural' => 'Solution Categories',
				'name' => 'solution-category',
				'slug' => 'solutions/category',
				'publicly_queryable' => false,
			],
			[
				'singular' => 'Solution Type',
				'plural' => 'Solution Types',
				'name' => 'solution-type',
				'slug' => 'solutions/type',
				'publicly_queryable' => false,
			],
		],
	],


// Loop through each configuration to register CPTs, Taxonomies, and Admin Filters
foreach ($custom_post_types_config as $post_type => $settings) {

	// Check if hierarchical mode is enabled for the post type
	$is_hierarchical = isset($settings['hierarchical']) ? $settings['hierarchical'] : false;

	// Set supported features: use custom supports if provided, otherwise fallback to defaults
	if (isset($settings['supports']) && is_array($settings['supports'])) {
		$supports = $settings['supports'];
	} else {
		$supports = ['title', 'editor', 'excerpt', 'thumbnail', 'author'];

		// Add page-attributes support if post type is hierarchical (adds Parent dropdown)
		if ($is_hierarchical) {
			$supports[] = 'page-attributes';
		}
	}

	// Set menu icon: use custom menu icon if provided, otherwise fallback to default
	$menu_icon = isset($settings['menu_icon']) ? $settings['menu_icon'] : 'dashicons-open-folder';

	// Set rewrite slug: use custom slug if provided, otherwise fallback to $post_type key
	$cpt_slug = isset($settings['slug']) ? $settings['slug'] : $post_type;

	// 1. Register Custom Post Type
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
		'hierarchical' => $is_hierarchical, // Set whether CPT is hierarchical
		'has_archive' => false,
		'public' => $settings['public'] ?? true,
		'show_ui' => true,
		'menu_position' => 20,
		'rewrite' => ($settings['public'] ?? true) ? ['slug' => $cpt_slug] : false,
		'show_in_rest' => true,
		'menu_icon' => $menu_icon,
		'supports' => $supports,
	]);

	// 2. Register Taxonomy if defined in configuration
	if (isset($settings['taxonomies'])) {
		foreach ($settings['taxonomies'] as $taxonomy) {
			$taxonomy_name = $taxonomy['name'];
			$publicly_queryable = isset($taxonomy['publicly_queryable']) ? $taxonomy['publicly_queryable'] : true;
			$rewrite = $publicly_queryable && isset($taxonomy['slug']) ? ['slug' => $taxonomy['slug']] : false;
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

	// 3. Add Author column in admin list dynamically using the post type key
	add_filter("manage_{$post_type}_posts_columns", function ($columns) {
		$new_columns = [];
		foreach ($columns as $key => $value) {
			$new_columns[$key] = $value;
			if ($key === 'title') {
				$new_columns['author'] = 'Author';
			}
		}
		return $new_columns;
	});
}