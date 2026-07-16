<?php
	// Check if this is being rendered in the backend editor
	if (is_admin()) {
		if (is_readable($file = __DIR__ . '/preview.php')) { require $file; return; } // Local preview.php
		if ($file = locate_template('template-parts/parts/acf-block-preview.php', false, false)) { require $file; return; } // Global fallback preview
		return; // Stop even if no preview found
	}

	$block_name = sanitize_title($block['title']);
	$class_name = $block_name;
	if ( ! empty( $block['className'] ) ) { $class_name .= ' ' . $block['className']; }
	$fields = get_fields();
?>

<div class="section <?= esc_attr( $class_name ); ?> inview-animate--fade">
	<div class="container">

		<?php rrp( $fields['desktop_image'] ?? null, $fields['mobile_image'] ?? null, [ 'picture_class' => 'bg', 'img_class' => 'cover', 'loading' => 'lazy', 'fetchpriority' => 'low', ] ); //$fields['desktop_image'] can be replaced by get_post_thumbnail_id($post->ID) if you need featured image of a post/page ?>

		<?= (($fields['caption']??null) != '') ? '<p class="text-center text-uppercase pt-2 inview-animate--fade">'.$fields['caption'].'</p>' : ''; ?>

	</div>
</div>
