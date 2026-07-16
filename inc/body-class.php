<?php
add_filter('body_class', function($c){
	// Device type
	$c[] = wp_is_mobile() ? 'mobile-device' : 'desktop-device';

	// TABLET detection (optional)

	/*
	$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
	$is_tablet = wp_is_mobile() && preg_match('/ipad|tablet|nexus 7|nexus 9|nexus 10|sm-t|tab|lenovo tab|xperia tablet|mi pad|pixel c/i', $ua);

	if ($is_tablet) {
		$c[] = 'tablet-device';
	}
	*/

	// Browser detection (WP globals cached)
	global $is_chrome, $is_safari, $is_IE, $is_edge, $is_gecko, $is_opera;

	if($is_chrome) $c[]='chrome';
	elseif($is_safari) $c[]='safari';
	elseif($is_edge) $c[]='edge';
	elseif($is_IE) $c[]='ie';
	elseif($is_gecko) $c[]='gecko';
	elseif($is_opera) $c[]='opera';
	else $c[]='unknown';

	// OS detection (cheap UA check)
	$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
	if(stripos($ua,'mac')!==false) $c[]='osx';
	elseif(stripos($ua,'linux')!==false) $c[]='linux';
	elseif(stripos($ua,'windows')!==false) $c[]='windows';
	elseif(stripos($ua,'android')!==false) $c[]='android';
	elseif(stripos($ua,'iphone')!==false || stripos($ua,'ipad')!==false) $c[]='ios';

	$c[]='frontend';

	return $c;
});