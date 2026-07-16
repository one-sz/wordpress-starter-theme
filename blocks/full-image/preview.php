<?php

	$block_name = sanitize_title($block['title']);
	$class_name = $block_name;
	if ( ! empty( $block['className'] ) ) { $class_name .= ' ' . $block['className']; }
	$fields = get_fields();

	$d_image = $fields['desktop_image'] ?? null;
	$m_image = $fields['mobile_image'] ?: $d_image; // fallback

?>

<div class="section <?= esc_attr( $class_name ); ?>">

	<picture>
		<source media="(max-width: 767px)" srcset="<?= wp_get_attachment_image_url($m_image, 'full') ?>">
		<source media="(min-width: 768px)" srcset="<?= wp_get_attachment_image_url($d_image, 'full') ?>">
		<img src="<?= wp_get_attachment_image_url($d_image, 'full') ?>" alt="<?= get_post_meta($d_image, '_wp_attachment_image_alt', true) ?>" class="w-100 h-100" fetchpriority="low" decoding="async">
	</picture>

</div>
