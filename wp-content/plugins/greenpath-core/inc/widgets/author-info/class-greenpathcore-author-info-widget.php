<?php

if ( ! function_exists( 'greenpath_core_add_author_info_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_author_info_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Author_Info_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_author_info_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Author_Info_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'greenpath_core_author_info' );
			$this->set_name( esc_html__( 'Nutterlygood Author Info', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Add author info element into widget areas', 'greenpath-core' ) );
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'author_username',
					'title'      => esc_html__( 'Author Username', 'greenpath-core' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type' => 'color',
					'name'       => 'author_color',
					'title'      => esc_html__( 'Author Color', 'greenpath-core' ),
				)
			);
		}

		public function render( $atts ) {
			$author_id = 1;
			if ( ! empty( $atts['author_username'] ) ) {
				$author = get_user_by( 'login', $atts['author_username'] );

				if ( ! empty( $author ) ) {
					$author_id = $author->ID;
				}
			}

			$author_link    = get_author_posts_url( $author_id );
			$author_bio     = get_the_author_meta( 'description', $author_id );
			$author_socials = greenpath_core_get_author_social_networks( $author_id );
			?>
			<div class="widget qodef-author-info">
				<a itemprop="url" class="qodef-author-info-image" href="<?php echo esc_url( $author_link ); ?>">
					<?php echo get_avatar( $author_id, 171 ); ?>
				</a>
				<?php if ( ! empty( $author_bio ) ) { ?>
					<h4 class="qodef-author-info-name vcard author">
						<a itemprop="url" href="<?php echo esc_url( $author_link ); ?>">
							<span class="fn"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></span>
						</a>
					</h4>
					<p itemprop="description" class="qodef-author-info-description"><?php echo esc_html( $author_bio ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $author_socials ) ) { ?>
					<div class="qodef-author-social-icons">
						<?php foreach ( $author_socials as $social ) { ?>
							<a itemprop="url" class="<?php echo esc_attr( $social['class'] ); ?>" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank">
								<?php echo greenpath_core_get_svg_icon( $social['network'] ); ?>
							</a>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<?php
		}
	}
}
