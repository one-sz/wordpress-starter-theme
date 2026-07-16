<?php
	// Check if this is being rendered in the backend editor
	if (is_admin()) {
		if (is_readable($file = __DIR__ . '/preview.php')) { require $file; return; } // Local preview.php
		if ($file = locate_template('template-parts/parts/acf-block-preview.php', false, false)) { require $file; return; } // Global fallback preview
		return; // Stop even if no preview found
	}

	$class_name = sanitize_title($block['title']);
	if (!empty($block['className'])) $class_name .= ' ' . $block['className'];
	$fields = get_fields();

	$min = isset($fields['mobile']) ? (float)$fields['mobile'] : 0;
	$max = isset($fields['desktop']) ? (float)$fields['desktop'] : 0;

	//NOTE: Do NOT forget to change the values at the beginning of the project according to design if needed
	$min_vw = 768; // Mobile breakpoint - ussualy I'd set to start the mobile from 768 because on tablets should be smaller
	$max_vw = 1440; // Desktop breakpoint

	// Prevent division by zero
	if ($max_vw === $min_vw) {
		$fluid = "{$min}px";
	} else {
		$s = round((($max - $min) / ($max_vw - $min_vw)) * 100, 2);
		$i = round($min - ($s * $min_vw / 100), 2);
		$fluid = "clamp(" . min($min, $max) . "px, {$s}vw + {$i}px, " . max($min, $max) . "px)";
	}
?>

<div class="section <?= esc_attr($class_name); ?>" style="height: <?= $fluid; ?>"></div>