<div class="wrap about-wrap qodef-welcome-page">
	<div class="qodef-welcome-page-heading">
		<h1 class="qodef-welcome-page-title">
			<?php
			echo sprintf( esc_html__( 'Welcome to %s', 'nuttergood' ), esc_attr( $theme_name ) );
			?>
			<small><?php echo esc_html( $theme_version ); ?></small>
		</h1>
	</div>
	<div class="qodef-welcome-page-text">
		<?php
		echo sprintf(
			esc_html__( 'Thank you for installing %1$s - %2$s! Install the required plugins below to unlock all theme features and start building your site.', 'nuttergood' ),
			esc_attr( $theme_name ),
			esc_attr( $theme_description )
		);
		?>
	</div>
	<div class="qodef-welcome-page-content">
		<div class="qodef-welcome-page-screenshot">
			<img src="<?php echo esc_url( $theme_screenshot ); ?>" alt="<?php esc_attr_e( 'Theme Screenshot', 'nuttergood' ); ?>"/>
		</div>
		<div class="qodef-welcome-page-links-holder">
			<div class="qodef-welcome-page-install-core">
				<p><?php esc_html_e( 'Please install and activate required plugins in order to gain access to all the theme functionalities and features.', 'nuttergood' ); ?></p>
				<a class="qodef-welcome-page-install-button" href="<?php echo add_query_arg( array( 'page' => 'install-required-plugins&plugin_status=install' ), esc_url( admin_url( 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Install Required Plugins', 'nuttergood' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>