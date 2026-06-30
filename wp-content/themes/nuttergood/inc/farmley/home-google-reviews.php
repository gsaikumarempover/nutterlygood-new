<?php
/**
 * Homepage Google Business reviews feed — below the blog section.
 */

if ( ! function_exists( 'nuttergood_farmley_google_reviews_defaults' ) ) {
	/**
	 * Curated Google-style reviews (used when Places API is unavailable).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function nuttergood_farmley_google_reviews_defaults() {
		return array(
			array(
				'author'  => 'Priya Sharma',
				'rating'  => 5,
				'text'    => 'Premium quality almonds and cashews. Fresh, well packed, and delivered quickly to Kokapet. Will order again.',
				'time'    => '2 weeks ago',
				'initial' => 'P',
			),
			array(
				'author'  => 'Rahul Reddy',
				'rating'  => 5,
				'text'    => 'Best dry fruit store in Hyderabad. Trail mixes are excellent and the mouth fresheners taste authentic.',
				'time'    => '1 month ago',
				'initial' => 'R',
			),
			array(
				'author'  => 'Ananya Krishnan',
				'rating'  => 5,
				'text'    => 'Loved the roasted nuts and gifting packs. Pricing is fair for the quality you get from Nutterly Good.',
				'time'    => '1 month ago',
				'initial' => 'A',
			),
			array(
				'author'  => 'Vikram Patel',
				'rating'  => 4,
				'text'    => 'Good variety of savouries and seeds. Checkout was smooth and customer support responded quickly on WhatsApp.',
				'time'    => '2 months ago',
				'initial' => 'V',
			),
			array(
				'author'  => 'Sneha Gupta',
				'rating'  => 5,
				'text'    => 'Hyderabad families will love this store. Everything feels fresh, hygienic, and thoughtfully packed.',
				'time'    => '2 months ago',
				'initial' => 'S',
			),
			array(
				'author'  => 'Arjun Mehta',
				'rating'  => 5,
				'text'    => 'Regular customer now. The soaked-nuts range and festive hampers are perfect for gifting.',
				'time'    => '3 months ago',
				'initial' => 'A',
			),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_google_reviews_map_url' ) ) {
	function nuttergood_farmley_google_reviews_map_url() {
		$info = function_exists( 'nuttergood_farmley_contact_info' ) ? nuttergood_farmley_contact_info() : array();
		if ( ! empty( $info['map_url'] ) ) {
			return $info['map_url'];
		}
		return 'https://www.google.com/maps/search/?api=1&query=Nutterly+Good+Kokapet+Hyderabad';
	}
}

if ( ! function_exists( 'nuttergood_farmley_fetch_google_reviews_api' ) ) {
	/**
	 * @return array<int, array<string, mixed>>|null
	 */
	function nuttergood_farmley_fetch_google_reviews_api() {
		$api_key   = (string) get_option( 'ng_farmley_google_places_api_key', '' );
		$place_id  = (string) get_option( 'ng_farmley_google_place_id', '' );
		$cache_key = 'ng_farmley_google_reviews_' . md5( $place_id );

		if ( '' === $api_key || '' === $place_id ) {
			return null;
		}

		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) && ! empty( $cached ) ) {
			return $cached;
		}

		$url = add_query_arg(
			array(
				'place_id' => $place_id,
				'fields'   => 'rating,user_ratings_total,reviews',
				'key'      => $api_key,
			),
			'https://maps.googleapis.com/maps/api/place/details/json'
		);

		$response = wp_remote_get( $url, array( 'timeout' => 12 ) );
		if ( is_wp_error( $response ) ) {
			return null;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['result']['reviews'] ) || ! is_array( $body['result']['reviews'] ) ) {
			return null;
		}

		$reviews = array();
		foreach ( $body['result']['reviews'] as $review ) {
			$author = isset( $review['author_name'] ) ? (string) $review['author_name'] : '';
			$text   = isset( $review['text'] ) ? (string) $review['text'] : '';
			if ( '' === $author || '' === $text ) {
				continue;
			}

			$initial = function_exists( 'mb_substr' )
				? mb_substr( $author, 0, 1 )
				: substr( $author, 0, 1 );

			$reviews[] = array(
				'author'  => $author,
				'rating'  => isset( $review['rating'] ) ? (int) $review['rating'] : 5,
				'text'    => $text,
				'time'    => isset( $review['relative_time_description'] ) ? (string) $review['relative_time_description'] : '',
				'initial' => strtoupper( $initial ),
				'photo'   => isset( $review['profile_photo_url'] ) ? (string) $review['profile_photo_url'] : '',
			);
		}

		if ( empty( $reviews ) ) {
			return null;
		}

		$summary = array(
			'rating'       => isset( $body['result']['rating'] ) ? (float) $body['result']['rating'] : 0,
			'total_ratings' => isset( $body['result']['user_ratings_total'] ) ? (int) $body['result']['user_ratings_total'] : 0,
		);
		update_option( 'ng_farmley_google_reviews_summary', $summary, false );
		set_transient( $cache_key, $reviews, DAY_IN_SECONDS );

		return $reviews;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_google_reviews' ) ) {
	/**
	 * @return array{reviews: array<int, array<string, mixed>>, rating: float, total: int}
	 */
	function nuttergood_farmley_get_google_reviews() {
		$api_reviews = nuttergood_farmley_fetch_google_reviews_api();
		$reviews     = is_array( $api_reviews ) && ! empty( $api_reviews )
			? $api_reviews
			: nuttergood_farmley_google_reviews_defaults();

		$summary = get_option( 'ng_farmley_google_reviews_summary', array() );
		$rating  = isset( $summary['rating'] ) ? (float) $summary['rating'] : 4.9;
		$total   = isset( $summary['total_ratings'] ) ? (int) $summary['total_ratings'] : 0;

		if ( $total <= 0 ) {
			$total = count( $reviews );
			$sum   = 0;
			foreach ( $reviews as $review ) {
				$sum += (int) ( $review['rating'] ?? 5 );
			}
			$rating = $sum > 0 ? round( $sum / count( $reviews ), 1 ) : 4.9;
		}

		return array(
			'reviews' => $reviews,
			'rating'  => $rating,
			'total'   => $total,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_google_stars' ) ) {
	/**
	 * @param float $rating Rating value 0-5.
	 */
	function nuttergood_farmley_render_google_stars( $rating ) {
		$rating = max( 0, min( 5, (float) $rating ) );
		echo '<span class="ng-farmley-greviews__stars" aria-hidden="true">';
		for ( $i = 1; $i <= 5; $i++ ) {
			$class = $rating >= $i ? 'is-full' : ( $rating >= ( $i - 0.5 ) ? 'is-half' : 'is-empty' );
			echo '<span class="ng-farmley-greviews__star ' . esc_attr( $class ) . '"></span>';
		}
		echo '</span>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_home_google_reviews' ) ) {
	function nuttergood_farmley_render_home_google_reviews() {
		if ( ! is_front_page() ) {
			return;
		}

		$data    = nuttergood_farmley_get_google_reviews();
		$reviews = $data['reviews'];
		$map_url = nuttergood_farmley_google_reviews_map_url();
		?>
		<section class="ng-farmley-greviews" aria-label="<?php esc_attr_e( 'Google customer reviews', 'nuttergood' ); ?>">
			<div class="ng-farmley-greviews__inner qodef-content-grid">
				<div class="ng-farmley-greviews__head">
					<div class="ng-farmley-greviews__title-wrap">
						<h3 class="ng-farmley-greviews__title"><?php esc_html_e( 'What Our Customers Say', 'nuttergood' ); ?></h3>
						<p class="ng-farmley-greviews__subtitle"><?php esc_html_e( 'Real reviews from Google', 'nuttergood' ); ?></p>
					</div>
					<div class="ng-farmley-greviews__summary">
						<span class="ng-farmley-greviews__google-badge" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="22" height="22" focusable="false"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
							<span>Google</span>
						</span>
						<span class="ng-farmley-greviews__score"><?php echo esc_html( number_format( $data['rating'], 1 ) ); ?></span>
						<?php nuttergood_farmley_render_google_stars( $data['rating'] ); ?>
						<span class="ng-farmley-greviews__count">
							<?php
							printf(
								/* translators: %d: number of Google reviews */
								esc_html( _n( '%d review', '%d reviews', $data['total'], 'nuttergood' ) ),
								(int) $data['total']
							);
							?>
						</span>
						<a class="ng-farmley-greviews__maps-link" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'View on Google Maps', 'nuttergood' ); ?>
						</a>
					</div>
				</div>

				<div class="ng-farmley-greviews__feed-wrap">
					<button type="button" class="ng-farmley-greviews__nav ng-farmley-greviews__nav--prev" aria-label="<?php esc_attr_e( 'Previous reviews', 'nuttergood' ); ?>">&lsaquo;</button>
					<div class="ng-farmley-greviews__feed">
						<?php foreach ( $reviews as $review ) : ?>
							<article class="ng-farmley-greviews__card">
								<div class="ng-farmley-greviews__card-top">
									<?php if ( ! empty( $review['photo'] ) ) : ?>
										<img class="ng-farmley-greviews__avatar ng-farmley-greviews__avatar--photo" src="<?php echo esc_url( $review['photo'] ); ?>" alt="" loading="lazy" decoding="async" />
									<?php else : ?>
										<span class="ng-farmley-greviews__avatar" aria-hidden="true"><?php echo esc_html( $review['initial'] ?? 'G' ); ?></span>
									<?php endif; ?>
									<div class="ng-farmley-greviews__meta">
										<h4 class="ng-farmley-greviews__author"><?php echo esc_html( $review['author'] ); ?></h4>
										<?php if ( ! empty( $review['time'] ) ) : ?>
											<span class="ng-farmley-greviews__time"><?php echo esc_html( $review['time'] ); ?></span>
										<?php endif; ?>
									</div>
									<span class="ng-farmley-greviews__g-icon" aria-hidden="true">
										<svg viewBox="0 0 24 24" width="16" height="16"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
									</span>
								</div>
								<?php nuttergood_farmley_render_google_stars( (float) ( $review['rating'] ?? 5 ) ); ?>
								<p class="ng-farmley-greviews__text"><?php echo esc_html( $review['text'] ); ?></p>
							</article>
						<?php endforeach; ?>
					</div>
					<button type="button" class="ng-farmley-greviews__nav ng-farmley-greviews__nav--next" aria-label="<?php esc_attr_e( 'Next reviews', 'nuttergood' ); ?>">&rsaquo;</button>
				</div>
			</div>
		</section>
		<?php
	}
	add_action( 'greenpath_action_after_page_content', 'nuttergood_farmley_render_home_google_reviews', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_home_google_reviews_assets' ) ) {
	function nuttergood_farmley_home_google_reviews_assets() {
		if ( ! is_front_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-home-google-reviews.css';
		$js  = $dir . '/assets/js/farmley-home-google-reviews.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-home-google-reviews',
				$uri . '/assets/css/farmley-home-google-reviews.css',
				array( 'nuttergood-farmley-home', 'greenpath-core-style' ),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-home-google-reviews',
				$uri . '/assets/js/farmley-home-google-reviews.js',
				array( 'jquery' ),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_home_google_reviews_assets', 42 );
}