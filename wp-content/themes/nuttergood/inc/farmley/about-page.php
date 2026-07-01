<?php
/**
 * About Us page — brand story + real product categories.
 */

if ( ! function_exists( 'nuttergood_farmley_is_about_page' ) ) {
	function nuttergood_farmley_is_about_page() {
		return is_page( 'about-us' ) || (int) get_queried_object_id() === 3431;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_term_link_safe' ) ) {
	function nuttergood_farmley_get_term_link_safe( $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		}
		$link = get_term_link( $term, 'product_cat' );
		return is_wp_error( $link ) ? home_url( '/shop/' ) : $link;
	}
}

if ( ! function_exists( 'nuttergood_farmley_about_categories' ) ) {
	function nuttergood_farmley_about_categories() {
		return array(
			array(
				'title'       => __( 'Dry Fruits', 'nuttergood' ),
				'description' => __( 'Premium almonds, raisins, pistachios and more — jewel-toned gifts of the earth, sourced for natural sweetness and wholesome goodness.', 'nuttergood' ),
				'image'       => 'ng-media/products/df-prclassicalmd-250-premium-classic-almonds-8.png',
				'url'         => nuttergood_farmley_get_term_link_safe( 'dry-fruits' ),
			),
			array(
				'title'       => __( 'Chips', 'nuttergood' ),
				'description' => __( 'Flavorful savory snacks made with quality ingredients — crunchy, bold, and a healthier alternative to everyday munching.', 'nuttergood' ),
				'image'       => 'ng-media/products/ch-mv-ch-150-mix-vegetable-chips-33.png',
				'url'         => nuttergood_farmley_get_term_link_safe( 'chips' ),
			),
			array(
				'title'       => __( 'Mixes', 'nuttergood' ),
				'description' => __( 'Thoughtfully blended trail mixes and fruit blends — perfect for gifting, travel, and elevating everyday snacking moments.', 'nuttergood' ),
				'image'       => 'ng-media/products/mx-mf-mx-250-masala-fruit-mix-80.png',
				'url'         => nuttergood_farmley_get_term_link_safe( 'mixes' ),
			),
			array(
				'title'       => __( 'Mouth Freshners', 'nuttergood' ),
				'description' => __( 'Traditional Indian mouth fresheners and mukhwas — refreshing, authentic flavours rooted in Hyderabad\'s culinary heritage.', 'nuttergood' ),
				'image'       => 'ng-media/products/anardana-goli-39.png',
				'url'         => nuttergood_farmley_get_term_link_safe( 'mouth-fresheners' ),
			),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_about_page' ) ) {
	function nuttergood_farmley_render_about_page() {
		$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		$hero_image = nuttergood_farmley_uploads_url( 'ng-media/about/ng-about-hero.jpg' );
		$categories = nuttergood_farmley_about_categories();
		?>
		<div class="ng-farmley-about">
			<section class="ng-farmley-about__intro qodef-content-grid">
				<div class="ng-farmley-about__intro-grid">
					<div class="ng-farmley-about__copy">
						<p class="ng-farmley-about__eyebrow"><?php esc_html_e( 'About Us', 'nuttergood' ); ?></p>
						<h2 class="ng-farmley-about__title"><?php esc_html_e( 'Born from Hyderabad — premium taste, healthy choices, fair prices', 'nuttergood' ); ?></h2>
						<p class="ng-farmley-about__text"><?php esc_html_e( 'The aroma of spices and the vibrant colors of nature\'s bounty filled the air in Hyderabad. For generations, access to the finest dry fruits — those jewel-toned gifts of the earth — remained a privilege, often priced beyond reach. Even the simple pleasure of a refreshing mouth freshener or a flavorful savory snack, made with quality ingredients, came with a hefty tag. Unhealthy snacking habits thrived, simply because healthier, tastier options felt out of reach.', 'nuttergood' ); ?></p>
						<p class="ng-farmley-about__text"><?php esc_html_e( 'But a new chapter began. Born from the heart of Hyderabad, a startup with a simple yet powerful vision emerged. We believed that everyone deserved access to premium quality dry fruits, the kind bursting with natural sweetness and wholesome goodness. We envisioned a world where snacking was not a compromise on health or taste, but an elevation of everyday moments.', 'nuttergood' ); ?></p>
						<p class="ng-farmley-about__text"><?php esc_html_e( 'We embarked on a journey to source the choicest almonds, the plumpest raisins, the most fragrant pistachios, and a delightful array of traditional Indian mouth fresheners and savories, all while keeping prices fair and accessible. We meticulously crafted each offering, ensuring that every bite was a testament to quality and flavor.', 'nuttergood' ); ?></p>
						<p class="ng-farmley-about__text"><?php esc_html_e( 'We wanted to replace the guilt of unhealthy eating with the joy of indulging in nature\'s finest. Our offerings became a bridge — connecting the desire for premium taste with the necessity of healthy choices. It wasn\'t just about selling snacks; it was about empowering people to make better choices, to savor authentic flavors, and to treat themselves well. Join us in this journey of flavorful well-being, one delicious and healthy bite at a time.', 'nuttergood' ); ?></p>
						<a class="ng-farmley-about__cta" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop our range', 'nuttergood' ); ?></a>
					</div>
					<div class="ng-farmley-about__visual">
						<img src="<?php echo esc_url( $hero_image ); ?>" alt="<?php esc_attr_e( 'Premium dry fruits, spices and Indian snacks by Nutterly Good', 'nuttergood' ); ?>" width="960" height="600" loading="lazy" />
					</div>
				</div>
			</section>

			<section class="ng-farmley-about__stats">
				<div class="ng-farmley-about__stats-inner qodef-content-grid">
					<div class="ng-farmley-about__stat">
						<strong>50+</strong>
						<span><?php esc_html_e( 'Curated products', 'nuttergood' ); ?></span>
					</div>
					<div class="ng-farmley-about__stat">
						<strong>100%</strong>
						<span><?php esc_html_e( 'Quality-first sourcing', 'nuttergood' ); ?></span>
					</div>
					<div class="ng-farmley-about__stat">
						<strong><?php esc_html_e( 'Free delivery', 'nuttergood' ); ?></strong>
						<span><?php esc_html_e( 'On orders above ₹2,500', 'nuttergood' ); ?></span>
					</div>
				</div>
			</section>

			<section class="ng-farmley-about__products qodef-content-grid">
				<div class="ng-farmley-about__section-head">
					<p class="ng-farmley-about__eyebrow"><?php esc_html_e( 'What we offer', 'nuttergood' ); ?></p>
					<h2 class="ng-farmley-about__title"><?php esc_html_e( 'Explore our product families', 'nuttergood' ); ?></h2>
					<p class="ng-farmley-about__text ng-farmley-about__text--center"><?php esc_html_e( 'Our products are not only healthy but flavorful, to pamper your taste buds — from premium dry fruits to savory snacks and traditional mouth fresheners.', 'nuttergood' ); ?></p>
				</div>
				<div class="ng-farmley-about__cards">
					<?php foreach ( $categories as $category ) : ?>
						<a class="ng-farmley-about__card" href="<?php echo esc_url( $category['url'] ); ?>">
							<div class="ng-farmley-about__card-media">
								<img src="<?php echo esc_url( nuttergood_farmley_uploads_url( $category['image'] ) ); ?>" alt="<?php echo esc_attr( $category['title'] ); ?>" width="400" height="400" loading="lazy" />
							</div>
							<div class="ng-farmley-about__card-body">
								<h3><?php echo esc_html( $category['title'] ); ?></h3>
								<p><?php echo esc_html( $category['description'] ); ?></p>
								<span class="ng-farmley-about__card-link"><?php esc_html_e( 'View collection', 'nuttergood' ); ?> →</span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="ng-farmley-about__promise">
				<div class="ng-farmley-about__promise-inner qodef-content-grid">
					<h2><?php esc_html_e( 'Goodness You Deserve', 'nuttergood' ); ?></h2>
					<p><?php esc_html_e( 'Our products are not only healthy but flavorful, to pamper your taste buds. Nutterly Good brings the "Goodness You Deserve" — premium taste, healthy choices, and authentic Indian flavours in every pack.', 'nuttergood' ); ?></p>
				</div>
			</section>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_setup_about_template' ) ) {
	function nuttergood_farmley_setup_about_template() {
		if ( get_option( 'ng_farmley_about_layout_v1' ) ) {
			return;
		}

		$page = get_page_by_path( 'about-us' );
		if ( ! $page ) {
			return;
		}

		update_post_meta( $page->ID, '_wp_page_template', 'page-about.php' );
		update_post_meta( $page->ID, 'qodef_enable_page_title', 'yes' );
		update_post_meta( $page->ID, 'qodef_page_title_color', '#FFFFFF' );
		delete_post_meta( $page->ID, '_elementor_edit_mode' );
		delete_post_meta( $page->ID, '_elementor_data' );
		delete_post_meta( $page->ID, '_elementor_css' );
		delete_post_meta( $page->ID, '_elementor_version' );

		update_option( 'ng_farmley_about_layout_v1', 1, false );
	}
	add_action( 'init', 'nuttergood_farmley_setup_about_template', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_about_assets' ) ) {
	function nuttergood_farmley_about_assets() {
		if ( ! nuttergood_farmley_is_about_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-about.css';
		if ( file_exists( $css ) ) {
			wp_enqueue_style( 'nuttergood-farmley-about', $uri . '/assets/css/farmley-about.css', array( 'greenpath-style' ), filemtime( $css ) );
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_about_assets', 35 );
}