<?php
/**
 * WooCommerce product editor — description intro + bullet points.
 */

if ( ! function_exists( 'nuttergood_farmley_add_product_data_tab' ) ) {
	function nuttergood_farmley_add_product_data_tab( $tabs ) {
		$tabs['ng_farmley_story'] = array(
			'label'    => __( 'Product Story', 'nuttergood' ),
			'target'   => 'ng_farmley_story_panel',
			'class'    => array(),
			'priority' => 65,
		);

		return $tabs;
	}

	add_filter( 'woocommerce_product_data_tabs', 'nuttergood_farmley_add_product_data_tab' );
}

if ( ! function_exists( 'nuttergood_farmley_render_product_data_panel' ) ) {
	function nuttergood_farmley_render_product_data_panel() {
		global $post;

		$product_id = $post ? (int) $post->ID : 0;
		$intro      = (string) get_post_meta( $product_id, '_ng_description_intro', true );
		$bullets    = (string) get_post_meta( $product_id, '_ng_description_bullets', true );
		$highlights = nuttergood_farmley_get_product_highlights( wc_get_product( $product_id ) );
		$selected   = array_column( $highlights, 'key' );
		$presets    = nuttergood_farmley_highlight_presets();
		?>
		<div id="ng_farmley_story_panel" class="panel woocommerce_options_panel hidden">
			<div class="options_group">
				<p class="form-field">
					<label for="ng_description_intro"><?php esc_html_e( 'Description', 'nuttergood' ); ?></label>
					<textarea id="ng_description_intro" name="ng_description_intro" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Short product description…', 'nuttergood' ); ?>"><?php echo esc_textarea( $intro ); ?></textarea>
				</p>
				<p class="form-field">
					<label for="ng_description_bullets"><?php esc_html_e( 'Bullet points', 'nuttergood' ); ?></label>
					<textarea id="ng_description_bullets" name="ng_description_bullets" rows="6" class="large-text" placeholder="<?php esc_attr_e( "One point per line\ne.g. Gluten Free\nNo Preservatives", 'nuttergood' ); ?>"><?php echo esc_textarea( $bullets ); ?></textarea>
					<span class="description"><?php esc_html_e( 'Shown as a simple list under the description on the product page.', 'nuttergood' ); ?></span>
				</p>
			</div>

			<div class="options_group">
				<p><strong><?php esc_html_e( 'Quick-add bullets', 'nuttergood' ); ?></strong></p>
				<p class="description"><?php esc_html_e( 'Tick to include preset labels in bullets (if bullet points field is empty).', 'nuttergood' ); ?></p>
				<div class="ng-farmley-admin-highlights">
					<?php foreach ( $presets as $key => $preset ) : ?>
						<label class="ng-farmley-admin-highlight">
							<input type="checkbox" name="ng_product_highlights[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $selected, true ) ); ?> />
							<?php echo esc_html( $preset['label'] ); ?>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	add_action( 'woocommerce_product_data_panels', 'nuttergood_farmley_render_product_data_panel' );
}

if ( ! function_exists( 'nuttergood_farmley_save_product_story_meta' ) ) {
	function nuttergood_farmley_save_product_story_meta( $product_id ) {
		if ( ! current_user_can( 'edit_post', $product_id ) ) {
			return;
		}

		$intro = isset( $_POST['ng_description_intro'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ng_description_intro'] ) ) : '';
		update_post_meta( $product_id, '_ng_description_intro', $intro );

		$bullets = isset( $_POST['ng_description_bullets'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ng_description_bullets'] ) ) : '';
		update_post_meta( $product_id, '_ng_description_bullets', $bullets );

		$presets  = nuttergood_farmley_highlight_presets();
		$selected = isset( $_POST['ng_product_highlights'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['ng_product_highlights'] ) ) : array();
		$highlight = array();

		foreach ( $selected as $key ) {
			if ( isset( $presets[ $key ] ) ) {
				$highlight[] = array( 'key' => $key, 'image_id' => 0 );
			}
		}

		update_post_meta( $product_id, '_ng_product_highlights', wp_json_encode( $highlight ) );
		delete_post_meta( $product_id, '_ng_story_blocks' );
	}

	add_action( 'woocommerce_process_product_meta', 'nuttergood_farmley_save_product_story_meta' );
}

if ( ! function_exists( 'nuttergood_farmley_admin_product_assets' ) ) {
	function nuttergood_farmley_admin_product_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'product' !== $screen->id ) {
			return;
		}

		$css = get_template_directory() . '/assets/css/farmley-product-admin.css';
		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-product-admin',
				get_template_directory_uri() . '/assets/css/farmley-product-admin.css',
				array(),
				filemtime( $css )
			);
		}
	}

	add_action( 'admin_enqueue_scripts', 'nuttergood_farmley_admin_product_assets' );
}
