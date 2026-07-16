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

<div class="section <?= esc_attr( $class_name ); ?> position-relative inview-animate--fade">
	<div class="container position-relative">

		<div class="swiper">

			<div class="items swiper-wrapper">
				<?php foreach ( $fields['images'] as $i=>$item ) { ?>
					<div class="item swiper-slide">
						<?php rrp( $fields['desktop_image'] ?? null, $fields['mobile_image'] ?? null, [ 'picture_class' => 'bg', 'img_class' => 'cover', 'loading' => 'lazy', 'fetchpriority' => 'low', ] ); ?>
					</div>
				<?php } ?>
			</div>

			<div class="swiper-button swiper-button-next"></div>
			<div class="swiper-button swiper-button-prev"></div>

			<div class="swiper-pagination"></div>

		</div>

	</div>
</div>
