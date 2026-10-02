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
 *
 * @param int|null $desktop_id The desktop image ID.
 * @param int|array|null $mobile_id The mobile image ID or options array if skipped.
 * @param array $args Optional attributes.
 */
function rrp($desktop_id, $mobile_id = null, $args = []) {
	if (!$desktop_id) return;
	if (is_array($mobile_id)) { $args = $mobile_id; $mobile_id = null; }

	$opt = array_merge([
		'picture_class' => 'bg',
		'img_class' => 'w-100 h-100',
		'loading' => 'lazy',
		'fetchpriority' => 'low',
		'decoding' => 'async',
		'sizes' => '100vw',
		'breakpoint' => 768,
	], $args);

	// Optimized helper to process image sources and metadata
	$get_img_data = function($id) {
		if (!$id || !($url = wp_get_attachment_image_url($id, 'full'))) return null;
		$path = get_attached_file($id);
		$srcset = wp_get_attachment_image_srcset($id, 'full');
		$meta = wp_get_attachment_metadata($id);

		$has_webp = $path && file_exists(preg_replace('/\.[^.]+$/', '.webp', $path));

		return (object)[
			'u' => $url,
			'ss' => $srcset,
			'w' => $meta['width'] ?? 0,
			'h' => $meta['height'] ?? 0,
			'wss' => ($has_webp && $srcset) ? preg_replace('/\.[^.]+(?=\s+\d+w)/', '.webp', $srcset) : '',
		];
	};

	if (!($desktop = $get_img_data($desktop_id))) return;
	$mobile = $get_img_data($mobile_id);

	$alt = esc_attr(get_post_meta($desktop_id, '_wp_attachment_image_alt', true) ?: get_the_title($desktop_id));
	$sizes = esc_attr($opt['sizes']);

	// Helper to render source tags cleanly
	$render_sources = function($img, $media = '') use ($sizes) {
		$html = '';
		$m_attr = $media ? ' media="' . esc_attr($media) . '"' : '';
		if ($img->wss) $html .= sprintf('<source%s srcset="%s" type="image/webp" sizes="%s">', $m_attr, esc_attr($img->wss), $sizes);
		if ($img->ss)  $html .= sprintf('<source%s srcset="%s" sizes="%s">', $m_attr, esc_attr($img->ss), $sizes);
		return $html;
	};

	$sources_html = '';
	if ($mobile) {
		$sources_html .= $render_sources($mobile, '(max-width:' . ($opt['breakpoint'] - 1) . 'px)');
		$sources_html .= $render_sources($desktop, '(min-width:' . $opt['breakpoint'] . 'px)');
	} else {
		$sources_html .= $render_sources($desktop);
	}

	printf(
		'<picture class="%s">%s<img class="%s" src="%s" alt="%s" sizes="%s" width="%d" height="%d" fetchpriority="%s" decoding="%s" loading="%s"></picture>',
		esc_attr($opt['picture_class']),
		$sources_html,
		esc_attr($opt['img_class']),
		esc_url($desktop->u),
		$alt,
		$sizes,
		$desktop->w,
		$desktop->h,
		esc_attr($opt['fetchpriority']),
		esc_attr($opt['decoding']),
		esc_attr($opt['loading'])
	);
}

// Filter output HTML of wp_get_attachment_image to use .webp (for any other JPG or PNG images that are not served through RRP function)
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment) {
	if (empty($attr['src'])) return $attr;
	$file = get_attached_file($attachment->ID);
	if (!$file) return $attr;
	// Fast memory check using static cache to prevent repeated disk I/O on the same page load
	static $webp_cache = [];
	$has_webp = $webp_cache[$attachment->ID] ??= file_exists(preg_replace('/\.\w+$/', '.webp', $file));
	if (!$has_webp) return $attr;
	// Pattern to swap .jpg, .jpeg, or .png with .webp safely
	$pattern = '/\.(?:jpe?g|png)(?=$|[\s,?])/i';
	$attr['src'] = preg_replace($pattern, '.webp', $attr['src']);
	if (!empty($attr['srcset'])) { $attr['srcset'] = preg_replace($pattern, '.webp', $attr['srcset']); }
	return $attr;
}, 10, 2);

//Disable auto-sizes for img
add_filter('wp_img_tag_add_auto_sizes', '__return_false');