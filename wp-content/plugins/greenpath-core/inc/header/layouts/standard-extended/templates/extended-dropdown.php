<div class="qodef-extended-dropdown-menu">
	<div class="qodef-extended-dropdown-menu-inner">
		<?php if ( ! empty( $opener_title ) ) { ?>
			<div class="qodef-extended-dropdown-opener">
				<?php echo greenpath_core_get_svg_icon( 'hamburger', 'qodef-opener-icon' ); ?>
				<span class="qodef-extended-dropdown-opener-text"><?php echo esc_html( $opener_title ); ?></span>
				<?php echo greenpath_core_get_svg_icon('arrow-dropdown', 'qodef-extended-dropdown-arrow' ); ?>
			</div>
		<?php } ?>
		<?php
		// Set main navigation menu as extended if extended navigation is not set
		$theme_location = has_nav_menu( 'extended-dropdown-menu' ) ? 'extended-dropdown-menu' : 'main-navigation';

		wp_nav_menu(
			array(
				'theme_location' => $theme_location,
				'container'      => '',
				'menu_class'     => 'qodef-extended-dropdown',
				'link_before'    => '<span class="qodef-menu-item-text">',
				'link_after'     => '</span>',
				'walker'         => new GreenPathCoreRootMainMenuWalker(),
				'menu_area'      => 'mobile',
			)
		);
		?>
	</div>
</div>
