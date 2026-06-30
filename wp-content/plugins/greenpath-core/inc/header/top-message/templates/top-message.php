<?php
$top_message      = greenpath_core_get_post_value_through_levels( 'qodef_top_message' );
$top_message_link = greenpath_core_get_post_value_through_levels( 'qodef_top_message_link' );
?>
<div id="qodef-top-message-holder" <?php qode_framework_class_attribute( apply_filters( 'greenpath_core_filter_top_message_class', array() ) ); ?>>
	<div class="qodef-top-message-inner">
		<?php if ( ! empty( $top_message_link ) ) { ?>
			<a class="qodef-top-message" href="<?php echo esc_url( $top_message_link ); ?>" target="_blank">
		<?php } ?>
			<?php echo qode_framework_wp_kses_html( 'content', $top_message ); ?>
		<?php if ( ! empty( $top_message_link ) ) { ?>
			</a>
		<?php } ?>
		<span class="qodef-close-message">
			<?php echo greenpath_core_get_svg_icon( 'close' ); ?>
        </span>
	</div>
</div>
