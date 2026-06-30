<div class="qodef-core-dashboard wrap about-wrap">
	<div class="qodef-cd-title-holder">
		<h1 class="qodef-cd-title"><?php echo sprintf( esc_html__( 'Welcome to %s', 'greenpath-core' ), $theme_name ); ?></h1>
	</div>
	<h4 class="qodef-cd-subtitle"><?php echo sprintf( esc_html__( 'Thank you for choosing %s. Now it\'s time to create something awesome.', 'greenpath-core' ), $theme_name ); ?></h4>
	<div class="qodef-core-dashboard-inner">
		<div class="qodef-core-dashboard-column">
			<div class="qodef-core-dashboard-box">
				<div class="qodef-cd-box-title-holder">
					<h2><?php esc_html_e( 'System Information', 'greenpath-core' ); ?></h2>
					<p><?php esc_html_e( 'Here is an overview of your current server configuration info.', 'greenpath-core' ); ?></p>
				</div>
				<div class="qodef-cd-box-inner">
					<?php
					foreach ( $system_info as $system_info_key => $system_info_value ) :
						$class = ( isset( $system_info_value['pass'] ) && ! $system_info_value['pass'] ) ? 'qodef-cdb-value-false' : '';
						?>
						<div class="qodef-cd-box-row">
							<div class="qodef-cdb-label"><?php echo esc_attr( $system_info_value['title'] ); ?></div>
							<div class="qodef-cdb-value <?php echo esc_attr( $class ); ?>">
								<span><?php echo wp_kses_post( $system_info_value['value'] ); ?></span>
								<?php if ( isset( $system_info_value['notice'] ) && ( isset( $system_info_value['pass'] ) && ! $system_info_value['pass'] ) ) { ?>
									<?php echo esc_html( $system_info_value['notice'] ); ?>
								<?php } ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="qodef-core-dashboard-column qodef-cd-smaller-column">
			<div class="qodef-core-dashboard-box">
				<div class="qodef-cd-box-title-holder">
					<h2><?php esc_html_e( 'Getting Started', 'greenpath-core' ); ?></h2>
				</div>
				<div class="qodef-cd-box-inner">
					<p><?php esc_html_e( 'Use the theme options, Elementor, and the installed extensions to customize your site.', 'greenpath-core' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>