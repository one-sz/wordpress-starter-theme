<footer class="site-footer">
	<div class="container">

		<div class="d-lg-flex justify-content-between">
			<?php foreach ([1,2,3,4] as $i) if (is_active_sidebar("footer-widget-$i")) { ?>
				<div class="widget widget-<?= $i; ?>"><?php dynamic_sidebar("footer-widget-$i"); ?></div>
			<?php } ?>
		</div>

		Copyright © <?= date('Y'); ?> . All rights reserved.

	</div>
</footer>