<?php
	$field_objects = get_field_objects() ?: [];
	$find_text = function($f) use (&$find_text) { if (is_string($f) && strlen(trim($f)) > 5) return $f; if (is_array($f)) foreach ($f as $v) if ($t = $find_text($v)) return $t; return ''; };
	$text = '';
	foreach (array_slice($field_objects, 0, 4, true) as $f)
		if (!in_array($f['type'], ['true_false', 'select']) && ($text = $find_text($f['value']))) break;
	$words = preg_split('/\s+/', trim(wp_strip_all_tags($text)));
	$preview = implode(' ', array_slice($words, 0, 5)) . (count($words) > 5 ? '...' : '');
?>

<div class="section <?= sanitize_title($block['title']); ?> py-3 px-2">
	<h2 class="preview-title"><?= esc_html($preview ?: $block['title']); ?></h2>
	<div class="click-to-edit pt-2 pb-2">CLICK TO EDIT <?= (!empty($preview)) ? '<div>(' . $block['title'] . ')</div>' : ''; ?></div>
</div>