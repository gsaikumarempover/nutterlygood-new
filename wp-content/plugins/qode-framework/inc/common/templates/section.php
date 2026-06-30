<div class="qodef-section-wrapper col-12 <?php echo esc_attr( $class ); ?>" <?php echo qode_framework_get_inline_attrs( $dependency_data, true ); ?>>
	<div class="qodef-section-wrapper-inner">
		<div class="row">
			<?php
			$section_title = $this_object->get_title();

			if ( ! empty( $section_title ) ) {
				$section_icon  = $this_object->get_icon();
				$title_classes = array();

				if ( ! empty( $section_icon ) ) {
					$title_classes[] = 'qodef-has-icon';
				}
				?>
				<h2 class="qodef-title qodef-section-title col-12 <?php echo esc_attr( implode( ' ', $title_classes ) ); ?>">
					<?php if ( ! empty( $section_icon ) ) { ?>
						<span class="qodef-title-icon"><?php echo qode_framework_wp_kses_html( 'html', $section_icon ); ?></span>
					<?php } ?>
					<span class="qodef-title-label"><?php echo esc_html( $section_title ); ?></span>
				</h2>
			<?php } ?>
			<?php
			$section_description = $this_object->get_description();

			if ( ! empty( $section_description ) ) {
				?>
				<p class="qodef-description qodef-section-description col-12"><?php echo esc_html( $section_description ); ?></p>
			<?php } ?>
			<?php
			foreach ( $this_object->get_children() as $child ) {
				$child->render();
			}
			?>
		</div>
	</div>
</div>
