<?php
/* Register Team Custom Post Types */
register_post_type('team', [
	'labels' => [
		'name' => 'Team',
		'singular_name' => 'Team',
		'all_items' => 'All Members',
		'view_item' => 'View Member',
		'add_new_item' => 'Add Member',
		'add_new' => 'Add Member',
		'edit_item' => 'Edit Member',
	],
	'has_archive' => false, //usually should be disabled
	'public' => false, //disable permalink
	'show_ui' => true, //when permalink is disabled to be able to edit post this must be true
	'menu_position' => 20,
	'rewrite' => ['slug' => 'team'],
	'show_in_rest' => true,
	'menu_icon' => 'dashicons-buddicons-buddypress-logo',
	'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
]);


/* Register Department Taxonomy for Team posts */
register_taxonomy('team-department', 'team', [
	'labels' => [
		'name' => 'Departments',
		'singular_name' => 'Department',
	],
	'hierarchical' => true,
	'public' => true,
	'rewrite' => ['slug' => 'team-department'],
	'show_in_rest' => true,
	'show_admin_column' => true,
	'has_archive' => false,
	'query_var' => true,
	'publicly_queryable' => false,
]);


// Add columns in admin list
add_filter('manage_team_posts_columns', function ($columns) {
	$new_columns = [];
	foreach ($columns as $key => $value) {
		$new_columns[$key] = $value;
		if ($key === 'title') {
			$new_columns['author'] = 'Author';
			//$new_columns['categories'] = 'Categories';
		}
	}
	return $new_columns;
});
