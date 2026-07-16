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

<div class="section <?= esc_attr( $class_name ); ?>">
	<div class="container">

		<div class="form-wrap">
			<?php //if ($form_id = $fields['form_id']??'') { gravity_form($form_id, false, false, false, null, true); } ?>
		</div>

	</div>
</div>
