<?php
$social_icons = get_post_meta( get_the_ID(), 'qodef_team_member_social_icons', true );

if ( ! empty( $social_icons ) ) {
	$framework_icons = QodeFrameworkIcons::get_instance();
	?>
	<div class="qodef-social-icon">
		<?php
		foreach ( $social_icons as $icon ) {
			if ( ! empty( $icon['qodef_team_member_icon'] ) || ! empty( $icon['qodef_team_member_svg_path'] ) ) {
				$social_source = $icon['qodef_team_member_icon_source'];
				$social_link   = $icon['qodef_team_member_icon_link'];
				$social_target = ! empty( $icon['qodef_team_member_icon_target'] ) ? $icon['qodef_team_member_icon_target'] : '_blank';
				?>
				<a class="qodef-social-icon" href="<?php echo esc_url( $social_link ); ?>" target="<?php echo esc_attr( $social_target ); ?>">
					<?php
					if ( 'iconpack' === $social_source ) {
						$social_icon_pack = $icon['qodef_team_member_icon'];
						$social_icon_name = $framework_icons->get_formatted_icon_field_name( 'qodef_team_member_icon', $social_icon_pack, '-' );
						$social_icon      = $icon[ $social_icon_name ];

						if ( ! empty( $social_icon ) ) {
							echo qode_framework_wp_kses_html( 'html', $framework_icons->render_icon( $social_icon, $social_icon_pack ) );
						}
					} elseif ( 'svg_path' === $social_source ) {
						$social_svg = $icon['qodef_team_member_svg_path'];

						if ( ! empty( $social_svg ) ) {
							echo qode_framework_wp_kses_html( 'svg', $social_svg );
						}
					}
					?>
				</a>
			<?php } ?>
		<?php } ?>
	</div>
<?php } ?>
