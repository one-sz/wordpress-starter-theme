<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }


// CSS & JS login
add_action('login_enqueue_scripts', function () {
	wp_enqueue_style( 'my-login-css', get_template_directory_uri() . '/my-admin/login.css', [], filemtime(get_template_directory() . '/my-admin/login.css') );
	wp_enqueue_script('my-login-js', get_template_directory_uri() . '/my-admin/login.js', [], filemtime(get_template_directory() . '/my-admin/login.js'), true );
});


// Custom logo
add_action('login_enqueue_scripts', function () {
	$logo = get_theme_mod('site_logo_image');

	if ($logo) {
		$id = attachment_url_to_postid($logo);
		[$src] = wp_get_attachment_image_src($id, 'full') ?: [$logo];
	} else {
		$src = includes_url('images/w-logo-blue.png'); // fallback
	}

	?>
		<link rel="icon" type="image/png" sizes="16x16" href="<?= get_stylesheet_directory_uri(); ?>/favicon/favicon.ico">
		<style>
			#login h1 a {
				background-image: url('<?php echo esc_url($src); ?>') !important;
				background-size: contain !important;
				background-repeat: no-repeat !important;
				background-position: center !important;
			}
		</style>
	<?php
});


// Logo link → homepage
add_filter('login_headerurl', fn() => home_url('/'));
add_filter('login_headertext', fn() => get_bloginfo('name'));


// Custom footer text
add_action('login_footer', function () {
	echo '<p class="login-footer">© ' . date('Y') . ' ' . get_bloginfo('name') . '</p>';
});
