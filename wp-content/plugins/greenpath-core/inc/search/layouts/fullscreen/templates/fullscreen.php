<div class="qodef-fullscreen-search-holder qodef-m">
	<?php
	greenpath_core_get_opener_icon_html(
		array(
			'option_name'  => 'search',
			'custom_class' => 'qodef-m-close',
			'custom_icon'  => 'search',
		),
		false,
		true
	);
	?>
	<div class="qodef-m-inner">
		<form action="<?php echo esc_url( home_url( '/' ) ); ?>" class="qodef-m-form" method="get">
			<input type="text" placeholder="<?php esc_attr_e( 'Search for...', 'greenpath-core' ); ?>" name="s" class="qodef-m-form-field" autocomplete="off" required/>
			<?php
			greenpath_core_get_opener_icon_html(
				array(
					'html_tag'     => 'button',
					'option_name'  => 'search',
					'custom_icon'  => 'search',
					'custom_class' => 'qodef-m-form-submit',
				)
			);
			?>
			<div class="qodef-m-form-line"></div>
		</form>
	</div>
</div>
