<?php
// Helper Functions

// Get template as html
function render_template($template, $data = []) {
	if ($data) extract($data, EXTR_SKIP);
	ob_start();
	require get_stylesheet_directory() . "/template-parts/$template";
	//include( "/template-parts/{$template}" );
	return ob_get_clean();
}

// Site Logo
add_image_size('site-logo', 300, 120, false);
function html_site_logo() {
	if (!$url = get_theme_mod('site_logo_image')) return;

	$id = attachment_url_to_postid($url);
	$title = get_bloginfo('name');
	$alt = get_post_meta($id, '_wp_attachment_image_alt', true) ?: $title;

	echo sprintf(
		'<a href="%s" class="site-logo-link d-inline-block" rel="home" aria-label="%s Homepage">%s</a>',
		esc_url(home_url('/')),
		esc_attr($title),
		wp_get_attachment_image( $id, 'site-logo', false, [ 'class' => 'site-logo', 'decoding' => 'async', 'fetchpriority' => 'high', 'alt' => esc_attr($alt), ] )
	);
}

//Get SVG inline from Media Library
function inline_svg_from_media($attachment_id) {
	if (!$attachment_id) return '<!-- Missing SVG attachment ID -->';

	$file_path = get_attached_file($attachment_id);

	if (!$file_path || !file_exists($file_path)) {
		return '<!-- SVG file not found -->';
	}

	$ext = pathinfo($file_path, PATHINFO_EXTENSION);
	if (strtolower($ext) !== 'svg') {
		return '<!-- Not an SVG file -->';
	}

	$svg = file_get_contents($file_path);
	return $svg;
}
//in block put this "echo inline_svg_from_media($fields['image'])"; //in ACF must set Image ID

//Generate WebP versions only in Admin (Media upload context)
if (is_admin()) {
	// 1. JPG/PNG convert to WebP at upload
	add_filter('wp_generate_attachment_metadata', function ($meta, $id) {
		$file = get_attached_file($id);
		if (!preg_match('/\.(jpe?g|png)$/i', $file)) return $meta;

		// Collect all paths: original + secondary dimensions
		$files = [$file];
		if (!empty($meta['sizes'])) { $dir = dirname($file) . '/'; foreach ($meta['sizes'] as $s) $files[] = $dir . $s['file']; }

		foreach ($files as $f) {
			$webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $f);
			// Skip if already generated
			if (file_exists($webp)) continue;
			// Handle PNG separately to preserve transparency
			if (preg_match('/\.png$/i', $f)) {
				$img = imagecreatefrompng($f);
				imagepalettetotruecolor($img);
				imagealphablending($img, true);
				imagesavealpha($img, true);
				imagewebp($img, $webp, 82);//quality for conversion from PNG
				imagedestroy($img);
				continue;
			}

			// Use WP image editor for JPG
			$editor = wp_get_image_editor($f);
			if (!is_wp_error($editor)) {
				$editor->set_quality(82);//quality for conversion from JPG
				$editor->save($webp, 'image/webp');
			}
		}

		return $meta;
	}, 10, 2);

	// 2. DELETE (Upon removal from the Media Library)
	add_action('delete_attachment', function ($id) {
		$meta = wp_get_attachment_metadata($id);
		$file = get_attached_file($id);

		if (!$file || !preg_match('/\.(jpe?g|png)$/i', $file)) return;

		// We delete the WebP of the main image
		$main_webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
		if (file_exists($main_webp)) unlink($main_webp);

		// We delete the WebP files for all generated sizes
		if (!empty($meta['sizes'])) {
			$dir = dirname($file) . '/';
			foreach ($meta['sizes'] as $s) {
				$size_webp = $dir . preg_replace('/\.(jpe?g|png)$/i', '.webp', $s['file']);
				if (file_exists($size_webp)) unlink($size_webp);
			}
		}
	});
}


/**
* Displays a <picture> element with WebP and Mobile/Desktop support from ACF.
* @param int|null $desktop_id The desktop image ID.
* @param int|null $mobile_id The mobile image ID (optional).
* @param array $args Optional attributes (class, loading, fetchpriority, decoding).
*/
function rrp( $desktop_id, $mobile_id = null, $args = [] ) { //rrp is for render_responsive_picture
	// Run the function only in the frontend to protect performance in admin/Gutenberg
	if (!$desktop_id) { return; }

	// If the second parameter is an array, it means mobile_id was skipped and args were passed instead
	if (is_array($mobile_id)) { $args = $mobile_id; $mobile_id = null; }

	// Default settings combined with those sent via arguments
	$defaults = [
		'picture_class' => 'bg',
		'img_class' => 'w-100 h-100',
		'loading' => 'lazy',
		'fetchpriority' => 'low',
		'decoding' => 'async',
	];
	$options = array_merge($defaults, $args);

	// Internal helper for processing image data
	$get_img_data = function($id) {
		if (!$id) return null;
		$path = get_attached_file($id);
		$url = wp_get_attachment_image_url($id, 'full');
		$srcset = wp_get_attachment_image_srcset($id, 'full');
		// Check file_exists once here, not in HTML for optimization
		$webp_path = $path ? preg_replace('/\.[^.]+$/', '.webp', $path) : '';
		$has_webp = !empty($webp_path) && file_exists($webp_path);
		return (object)[
			'u' => $url,
			'ss' => $srcset,
			'has_webp' => $has_webp,
			'wu' => $url ? preg_replace('/\.[^.]+$/', '.webp', $url) : '',
			'wss' => $srcset ? preg_replace('/\.[^.]+(?=\s+\d+w)/', '.webp', $srcset) : ''
		];
	};

	$desktop = $get_img_data($desktop_id);
	$mobile = $get_img_data($mobile_id);
	$alt = get_post_meta($desktop_id, '_wp_attachment_image_alt', true) ?: get_the_title($desktop_id);

	// Prepare HTML sources in PHP to keep the template clean
	$sources_html = '';

	if ($mobile) {
		// Scenario A: Both mobile and desktop images exist
		if ($mobile->has_webp) { $sources_html .= '<source media="(max-width:767px)" srcset="' . esc_attr($mobile->wss) . '" type="image/webp" sizes="100vw">'; }
		if ($mobile->ss) { $sources_html .= '<source media="(max-width:767px)" srcset="' . esc_attr($mobile->ss) . '" sizes="100vw">'; }

		if ($desktop->has_webp) { $sources_html .= '<source media="(min-width:768px)" srcset="' . esc_attr($desktop->wss) . '" type="image/webp" sizes="100vw">'; }
		if ($desktop->ss) { $sources_html .= '<source media="(min-width:768px)" srcset="' . esc_attr($desktop->ss) . '" sizes="100vw">'; }
	} else {
		// Scenario B: Only desktop image exists (Fallback for all screen sizes)
		// We drop the media attribute so these apply to ALL screen widths, forcing WebP first
		if ($desktop->has_webp) { $sources_html .= '<source srcset="' . esc_attr($desktop->wss) . '" type="image/webp" sizes="100vw">'; }
		if ($desktop->ss) { $sources_html .= '<source srcset="' . esc_attr($desktop->ss) . '" sizes="100vw">'; }
	}
?>
	<picture class="<?php echo esc_attr($options['picture_class']); ?>">
		<?php echo $sources_html; ?>
		<img class="<?php echo esc_attr($options['img_class']); ?>" src="<?php echo esc_url($desktop->u); ?>" alt="<?php echo esc_attr($alt); ?>" sizes="100vw" fetchpriority="<?php echo esc_attr($options['fetchpriority']); ?>" decoding="<?php echo esc_attr($options['decoding']); ?>" loading="<?php echo esc_attr($options['loading']); ?>">
	</picture>
<?php
}
