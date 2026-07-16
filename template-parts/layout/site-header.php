<header class="site-header">
	<div class="container">
		<div class="inner d-flex justify-content-between align-items-center">

			<div class="logo">
				<?php html_site_logo(); ?>
			</div>

			<div class="nav-trigger-wrap d-lg-none">
				<button href="#" class="nav-trigger d-flex flex-column justify-content-between" type="button" aria-label="Open Menu" aria-controls="nav-wrap" aria-expanded="false">
					<span></span>
					<span></span>
					<span></span>
					<span class="screen-reader-shortcut"><?php echo __( 'Open Menu' ); ?></span>
				</button>
			</div>

			<nav class="nav-wrap" id="nav-wrap" aria-label="Main navigation">
				<?php wp_nav_menu(['theme_location' => 'primary', 'menu_id' => 'main-menu',]); ?>
			</nav>

		</div>
	</div>
</header><!-- #header -->