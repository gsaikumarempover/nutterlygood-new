<?php
// Unique ID for search form fields
$qodef_unique_id = 'qodef-search-form-' . wp_unique_id();
?>
<form role="search" method="get" class="qodef-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $qodef_unique_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'nuttergood' ); ?></label>
	<div class="qodef-search-form-inner clear">
		<input type="search" id="<?php echo esc_attr( $qodef_unique_id ); ?>" class="qodef-search-form-field" value="" name="s" placeholder="<?php esc_attr_e( 'Search', 'nuttergood' ); ?>" />
		<button type="submit" class="qodef-search-form-button qodef--button-inside qodef--has-icon"><?php greenpath_render_svg_icon( 'search' ); ?></button>
	</div>
</form>
