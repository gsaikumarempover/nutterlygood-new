<?php
$current_user = wp_get_current_user();
?>
<div class="qodef-logged-in-user qodef-m">
	<div class="qodef-m-user">
		<a href="<?php echo greenpath_membership_get_dashboard_page_url(); ?>" class="qodef-login-opener">
			<?php
			$current_user_id = $current_user->ID;
			$user_image      = get_avatar( $current_user_id, 28 );

			if ( ! empty( $user_image ) ) { ?>
				<span class="qodef-m-user-image"><?php echo qode_framework_wp_kses_html( 'img', $user_image ) ?></span>
			<?php } ?>
		</a>
	</div>
</div>
