<?php
/**
 * Farmley blog — modern archive + single layouts with mapped static content.
 */

if ( ! function_exists( 'nuttergood_farmley_is_blog_context' ) ) {
	function nuttergood_farmley_is_blog_context() {
		return is_singular( 'post' )
			|| is_home()
			|| is_category()
			|| is_tag()
			|| is_author()
			|| is_date();
	}
}

if ( ! function_exists( 'nuttergood_farmley_blog_page_config' ) ) {
	/**
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_blog_page_config() {
		return array(
			'hero_eyebrow'  => __( 'Nutterly Good Journal', 'nuttergood' ),
			'hero_title'    => __( 'Snack smarter stories', 'nuttergood' ),
			'hero_subtitle' => __( 'Practical guides on dry fruits, nuts, trail mixes, and everyday wellness — written for busy Hyderabad families who want better choices without the fuss.', 'nuttergood' ),
			'stats'         => array(
				array(
					'value' => '6',
					'label' => __( 'Expert guides', 'nuttergood' ),
				),
				array(
					'value' => '4',
					'label' => __( 'Snack topics', 'nuttergood' ),
				),
				array(
					'value' => '5 min',
					'label' => __( 'Avg. read time', 'nuttergood' ),
				),
			),
			'filters'       => array(
				array( 'slug' => 'all', 'label' => __( 'All stories', 'nuttergood' ) ),
				array( 'slug' => 'wellness', 'label' => __( 'Wellness', 'nuttergood' ) ),
				array( 'slug' => 'dry-fruits', 'label' => __( 'Dry fruits', 'nuttergood' ) ),
				array( 'slug' => 'smart-snacking', 'label' => __( 'Smart snacking', 'nuttergood' ) ),
				array( 'slug' => 'mouth-fresheners', 'label' => __( 'Mouth fresheners', 'nuttergood' ) ),
			),
			'sidebar_about' => __( 'From premium almonds to trail mixes and mukhwas, we share honest snack advice rooted in Hyderabad — so every bite feels fresh, flavourful, and thoughtfully chosen.', 'nuttergood' ),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_blog_article_data' ) ) {
	/**
	 * @return array<string, array<string, mixed>>
	 */
	function nuttergood_farmley_blog_article_data() {
		$uploads = content_url( 'uploads/' );

		return array(
			'protein-rich-trail-mixes-for-busy-days' => array(
				'kicker'         => __( 'Smart snacking for busy routines', 'nuttergood' ),
				'category'       => __( 'Smart snacking', 'nuttergood' ),
				'category_slug'  => 'smart-snacking',
				'read_time'      => __( '4 min read', 'nuttergood' ),
				'image'          => $uploads . 'ng-media/blog/ng-blog-trail-mix.jpg',
				'excerpt'        => __( 'Build a protein-forward trail mix that travels well, keeps you full, and helps you skip random sugary snacks during long workdays.', 'nuttergood' ),
				'intro'          => __( 'A good trail mix should do more than taste nice. It should keep you full, travel well, and help you avoid random sugary snacks during long workdays, school runs, or commute hours.', 'nuttergood' ),
				'takeaways'      => array(
					__( 'Choose mixes with nuts and seeds first, not sugar-coated fillers.', 'nuttergood' ),
					__( 'Keep portions ready in small jars or pouches to avoid overeating.', 'nuttergood' ),
					__( 'Pair trail mix with fruit or curd when you need a more complete mini-meal.', 'nuttergood' ),
				),
				'sections'       => array(
					array(
						'title' => __( 'What makes a trail mix protein-rich?', 'nuttergood' ),
						'body'  => __( 'Look for almonds, peanuts, cashews, pumpkin seeds, sunflower seeds, and roasted chana. These ingredients bring natural protein, good fats, and crunch without needing heavy processing.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Best time to eat it', 'nuttergood' ),
						'body'  => __( 'Trail mix works well between breakfast and lunch, before evening workouts, or during travel. The goal is to choose a small, satisfying portion before hunger turns into cravings.', 'nuttergood' ),
					),
					array(
						'title' => __( 'How to portion it right', 'nuttergood' ),
						'body'  => __( 'A small handful is enough for most snack breaks. If the mix includes dried fruit, keep the portion tighter and balance it with water or unsweetened tea.', 'nuttergood' ),
					),
				),
				'checklist'      => array(
					__( 'Whole nuts and seeds', 'nuttergood' ),
					__( 'Low added sugar', 'nuttergood' ),
					__( 'Balanced salt and spice', 'nuttergood' ),
					__( 'Fresh aroma and crisp bite', 'nuttergood' ),
				),
			),
			'a-guide-to-choosing-quality-almonds' => array(
				'kicker'         => __( 'Buy better almonds with confidence', 'nuttergood' ),
				'category'       => __( 'Dry fruits', 'nuttergood' ),
				'category_slug'  => 'dry-fruits',
				'read_time'      => __( '5 min read', 'nuttergood' ),
				'image'          => $uploads . 'ng-media/blog/ng-blog-quality-almonds.jpg',
				'excerpt'        => __( 'Learn how freshness, texture, and aroma reveal almond quality — whether you snack daily or pick gifting packs.', 'nuttergood' ),
				'intro'          => __( 'Quality almonds are easy to spot when you know what to check. Freshness, size, texture, and aroma all matter, especially when almonds are eaten daily or used for gifting.', 'nuttergood' ),
				'takeaways'      => array(
					__( 'Fresh almonds should smell clean and naturally nutty.', 'nuttergood' ),
					__( 'Avoid shrivelled pieces, oily surfaces, or bitter aftertaste.', 'nuttergood' ),
					__( 'Store almonds away from heat and moisture after opening.', 'nuttergood' ),
				),
				'sections'       => array(
					array(
						'title' => __( 'Check the look and feel', 'nuttergood' ),
						'body'  => __( 'Good almonds usually look even in colour, feel firm, and have a crisp bite. Too many broken, dull, or soft pieces can indicate poor handling or old stock.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Taste matters most', 'nuttergood' ),
						'body'  => __( 'An almond should taste mildly sweet and nutty. If it tastes bitter, stale, or waxy, it is better not to use it for daily snacking.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Choose based on use', 'nuttergood' ),
						'body'  => __( 'Whole almonds are great for snacking and gifting, sliced almonds are useful for desserts, and soaked almonds work well for morning routines.', 'nuttergood' ),
					),
				),
				'checklist'      => array(
					__( 'Uniform colour', 'nuttergood' ),
					__( 'Crisp texture', 'nuttergood' ),
					__( 'Clean nutty aroma', 'nuttergood' ),
					__( 'No bitterness', 'nuttergood' ),
				),
			),
			'how-to-store-dry-fruits-at-home' => array(
				'kicker'         => __( 'Keep dry fruits fresh for longer', 'nuttergood' ),
				'category'       => __( 'Dry fruits', 'nuttergood' ),
				'category_slug'  => 'dry-fruits',
				'read_time'      => __( '4 min read', 'nuttergood' ),
				'image'          => $uploads . 'ng-media/blog/ng-blog-store-dry-fruits.jpg',
				'excerpt'        => __( 'Simple storage habits protect crunch, flavour, and natural oils — so your premium nuts stay fresh week after week.', 'nuttergood' ),
				'intro'          => __( 'Dry fruits are sensitive to air, heat, and moisture. A few simple storage habits can protect their crunch, flavour, and natural oils for much longer.', 'nuttergood' ),
				'takeaways'      => array(
					__( 'Use airtight containers after opening the pack.', 'nuttergood' ),
					__( 'Keep nuts away from sunlight, steam, and strong-smelling foods.', 'nuttergood' ),
					__( 'Refrigerate premium nuts if you buy in larger quantities.', 'nuttergood' ),
				),
				'sections'       => array(
					array(
						'title' => __( 'Why airtight storage matters', 'nuttergood' ),
						'body'  => __( 'Exposure to air can make nuts lose crunch and develop a stale taste. Glass jars, steel tins, or good-quality food containers work well.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Where to store them', 'nuttergood' ),
						'body'  => __( 'Choose a cool, dry cabinet for everyday quantities. For bulk packs, refrigeration helps slow down oil oxidation and keeps the flavour cleaner.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Small packs are easier to manage', 'nuttergood' ),
						'body'  => __( 'If you snack daily, split a large pack into smaller jars. Open one jar at a time so the rest stays sealed and fresh.', 'nuttergood' ),
					),
				),
				'checklist'      => array(
					__( 'Airtight jar', 'nuttergood' ),
					__( 'Cool shelf', 'nuttergood' ),
					__( 'Dry spoon only', 'nuttergood' ),
					__( 'No direct sunlight', 'nuttergood' ),
				),
			),
			'healthy-snacking-with-nuts-and-seeds' => array(
				'kicker'         => __( 'Build a better snack habit', 'nuttergood' ),
				'category'       => __( 'Smart snacking', 'nuttergood' ),
				'category_slug'  => 'smart-snacking',
				'read_time'      => __( '4 min read', 'nuttergood' ),
				'image'          => $uploads . 'ng-media/blog/ng-blog-healthy-snacking.jpg',
				'excerpt'        => __( 'Nuts and seeds make snacking more balanced with crunch, minerals, and steady energy — no complicated prep required.', 'nuttergood' ),
				'intro'          => __( 'Nuts and seeds are a simple way to make snacking more balanced. They bring crunch, natural fats, minerals, and steady energy without complicated prep.', 'nuttergood' ),
				'takeaways'      => array(
					__( 'Mix nuts and seeds for better texture and nutrition variety.', 'nuttergood' ),
					__( 'Keep ready portions near your desk or bag.', 'nuttergood' ),
					__( 'Prefer roasted or natural options over heavily sweetened snacks.', 'nuttergood' ),
				),
				'sections'       => array(
					array(
						'title' => __( 'Start with a simple bowl', 'nuttergood' ),
						'body'  => __( 'Combine almonds, cashews, pumpkin seeds, raisins, and a light spice mix. This gives you crunch, sweetness, and satisfaction in one snack.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Make it routine-friendly', 'nuttergood' ),
						'body'  => __( 'Keep a small container in your work bag, car, or kitchen shelf. Good snacks are easier to choose when they are already within reach.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Balance taste and health', 'nuttergood' ),
						'body'  => __( 'Spiced nuts and seed mixes can be flavourful without becoming heavy. Look for clean seasoning and avoid snacks that feel oily or overly salty.', 'nuttergood' ),
					),
				),
				'checklist'      => array(
					__( 'Crunchy nuts', 'nuttergood' ),
					__( 'Seeds for variety', 'nuttergood' ),
					__( 'Light seasoning', 'nuttergood' ),
					__( 'Portion control', 'nuttergood' ),
				),
			),
			'why-soaked-dry-fruits-are-better-for-digestion' => array(
				'kicker'         => __( 'A gentler way to enjoy dry fruits', 'nuttergood' ),
				'category'       => __( 'Wellness', 'nuttergood' ),
				'category_slug'  => 'wellness',
				'read_time'      => __( '3 min read', 'nuttergood' ),
				'image'          => $uploads . 'ng-media/blog/ng-blog-soaked-dry-fruits.jpg',
				'excerpt'        => __( 'Soaking softens texture and makes morning snacking easier — a light ritual many Hyderabad families already swear by.', 'nuttergood' ),
				'intro'          => __( 'Soaking dry fruits is a traditional habit for a reason. It softens texture, makes morning snacking easier, and can feel lighter for many people.', 'nuttergood' ),
				'takeaways'      => array(
					__( 'Soaked almonds and raisins are easy morning additions.', 'nuttergood' ),
					__( 'Use clean water and avoid soaking for too long at room temperature.', 'nuttergood' ),
					__( 'Peel almonds after soaking if you prefer a softer bite.', 'nuttergood' ),
				),
				'sections'       => array(
					array(
						'title' => __( 'Why soaking changes the texture', 'nuttergood' ),
						'body'  => __( 'Water softens the outer layer and makes dry fruits easier to chew. This is helpful for kids, elders, and anyone who prefers a gentler start to the day.', 'nuttergood' ),
					),
					array(
						'title' => __( 'How long should you soak?', 'nuttergood' ),
						'body'  => __( 'Almonds are commonly soaked overnight. Raisins and figs need less time. Always rinse before eating and use fresh water for soaking.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Make it a morning ritual', 'nuttergood' ),
						'body'  => __( 'Prepare a small bowl at night and keep it covered. In the morning, pair soaked dry fruits with breakfast instead of eating them in a rush.', 'nuttergood' ),
					),
				),
				'checklist'      => array(
					__( 'Clean water', 'nuttergood' ),
					__( 'Covered bowl', 'nuttergood' ),
					__( 'Morning rinse', 'nuttergood' ),
					__( 'Small portions', 'nuttergood' ),
				),
			),
			'best-mouth-fresheners-for-after-meals' => array(
				'kicker'         => __( 'Fresh endings after every meal', 'nuttergood' ),
				'category'       => __( 'Mouth fresheners', 'nuttergood' ),
				'category_slug'  => 'mouth-fresheners',
				'read_time'      => __( '3 min read', 'nuttergood' ),
				'image'          => $uploads . 'ng-media/blog/ng-blog-mouth-fresheners.jpg',
				'excerpt'        => __( 'A good mukhwas should taste clean and feel light — the perfect finish after lunch, dinner, or festive meals.', 'nuttergood' ),
				'intro'          => __( 'A good mouth freshener should taste clean, feel light, and leave a pleasant finish after meals. The best blends balance sweetness, spice, and aroma.', 'nuttergood' ),
				'takeaways'      => array(
					__( 'Choose fresheners with fennel, seeds, herbs, and balanced sweetness.', 'nuttergood' ),
					__( 'Avoid blends that feel sticky, stale, or overly artificial.', 'nuttergood' ),
					__( 'Keep a small jar at home, office, or in your travel bag.', 'nuttergood' ),
				),
				'sections'       => array(
					array(
						'title' => __( 'What to look for', 'nuttergood' ),
						'body'  => __( 'Fennel, sesame, coriander seeds, and gentle spices create a refreshing taste profile. A good blend should smell bright and feel crisp.', 'nuttergood' ),
					),
					array(
						'title' => __( 'When to serve it', 'nuttergood' ),
						'body'  => __( 'Mouth fresheners are perfect after lunch, dinner, festive meals, or tea-time snacks. They also make a nice finishing touch when guests visit.', 'nuttergood' ),
					),
					array(
						'title' => __( 'Storage tip', 'nuttergood' ),
						'body'  => __( 'Keep the jar tightly closed. Seeds and spices can lose aroma when exposed to air for too long.', 'nuttergood' ),
					),
				),
				'checklist'      => array(
					__( 'Fresh aroma', 'nuttergood' ),
					__( 'Balanced sweetness', 'nuttergood' ),
					__( 'Crisp seeds', 'nuttergood' ),
					__( 'Airtight storage', 'nuttergood' ),
				),
			),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_blog_post_meta' ) ) {
	/**
	 * @param int $post_id Post ID.
	 *
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_get_blog_post_meta( $post_id = 0 ) {
		$post_id = $post_id > 0 ? $post_id : get_the_ID();
		$slug    = get_post_field( 'post_name', $post_id );
		$data    = nuttergood_farmley_blog_article_data();

		if ( ! empty( $data[ $slug ] ) ) {
			return $data[ $slug ];
		}

		return array(
			'kicker'        => __( 'Nutterly Good Journal', 'nuttergood' ),
			'category'      => __( 'Wellness', 'nuttergood' ),
			'category_slug' => 'wellness',
			'read_time'     => __( '4 min read', 'nuttergood' ),
			'image'         => '',
			'excerpt'       => wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 24, '...' ),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_ensure_blog_posts_page' ) ) {
	function nuttergood_farmley_ensure_blog_posts_page() {
		if ( get_option( 'page_for_posts' ) ) {
			return;
		}

		$page = get_page_by_path( 'blog' );
		if ( ! $page ) {
			$page_id = wp_insert_post(
				array(
					'post_title'   => __( 'Blog', 'nuttergood' ),
					'post_name'    => 'blog',
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => '',
				)
			);
			if ( is_wp_error( $page_id ) || ! $page_id ) {
				return;
			}
		} else {
			$page_id = (int) $page->ID;
		}

		update_option( 'page_for_posts', $page_id );
	}
	add_action( 'after_setup_theme', 'nuttergood_farmley_ensure_blog_posts_page', 12 );
}

if ( ! function_exists( 'nuttergood_farmley_blog_body_class' ) ) {
	function nuttergood_farmley_blog_body_class( $classes ) {
		if ( nuttergood_farmley_is_blog_context() ) {
			$classes[] = 'ng-farmley-blog';
		}
		return $classes;
	}
	add_filter( 'body_class', 'nuttergood_farmley_blog_body_class' );
}

if ( ! function_exists( 'nuttergood_farmley_blog_sidebar_layout' ) ) {
	function nuttergood_farmley_blog_sidebar_layout( $layout ) {
		if ( nuttergood_farmley_is_blog_context() ) {
			return 'sidebar-33-right';
		}

		return $layout;
	}
	add_filter( 'greenpath_filter_sidebar_layout', 'nuttergood_farmley_blog_sidebar_layout', 15 );
}

if ( ! function_exists( 'nuttergood_farmley_blog_force_active_sidebar' ) ) {
	function nuttergood_farmley_blog_force_active_sidebar( $active, $index ) {
		if ( nuttergood_farmley_is_blog_context() && function_exists( 'greenpath_get_sidebar_name' ) && greenpath_get_sidebar_name() === $index ) {
			return true;
		}

		return $active;
	}
	add_filter( 'is_active_sidebar', 'nuttergood_farmley_blog_force_active_sidebar', 10, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_blog_disable_page_title' ) ) {
	function nuttergood_farmley_blog_disable_page_title( $enabled ) {
		if ( nuttergood_farmley_is_blog_context() ) {
			return false;
		}

		return $enabled;
	}
	add_filter( 'greenpath_filter_enable_page_title', 'nuttergood_farmley_blog_disable_page_title', 100 );
}

if ( ! function_exists( 'nuttergood_farmley_blog_holder_classes' ) ) {
	function nuttergood_farmley_blog_holder_classes( $classes ) {
		if ( is_single() ) {
			$classes[] = 'ng-farmley-blog__single';
		} else {
			$classes[] = 'ng-farmley-blog__feed';
		}
		return $classes;
	}
	add_filter( 'greenpath_filter_blog_holder_classes', 'nuttergood_farmley_blog_holder_classes' );
}

if ( ! function_exists( 'nuttergood_farmley_blog_post_classes' ) ) {
	function nuttergood_farmley_blog_post_classes( $classes ) {
		if ( is_admin() || is_single() || ! in_the_loop() || ! is_main_query() ) {
			return $classes;
		}

		static $index = 0;
		++$index;

		if ( 1 === $index ) {
			$classes[] = 'ng-farmley-blog-card--featured';
		}

		$meta = nuttergood_farmley_get_blog_post_meta();
		if ( ! empty( $meta['category_slug'] ) ) {
			$classes[] = 'ng-farmley-blog-card--' . sanitize_html_class( $meta['category_slug'] );
		}

		return $classes;
	}
	add_filter( 'post_class', 'nuttergood_farmley_blog_post_classes', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_render_blog_archive_hero' ) ) {
	function nuttergood_farmley_render_blog_archive_hero() {
		if ( is_single() || ! nuttergood_farmley_is_blog_context() ) {
			return;
		}

		$config = nuttergood_farmley_blog_page_config();
		$title  = $config['hero_title'];
		$copy   = $config['hero_subtitle'];

		if ( is_category() ) {
			$title = single_cat_title( '', false );
			$copy  = category_description() ? wp_strip_all_tags( category_description() ) : $config['hero_subtitle'];
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = get_the_author();
		} elseif ( is_date() ) {
			$title = get_the_date( 'F Y' );
		}

		?>
		<section class="ng-farmley-blog-hero" aria-label="<?php esc_attr_e( 'Blog introduction', 'nuttergood' ); ?>">
			<div class="ng-farmley-blog-hero__inner">
				<div class="ng-farmley-blog-hero__copy">
					<p class="ng-farmley-blog-hero__eyebrow"><?php echo esc_html( $config['hero_eyebrow'] ); ?></p>
					<h1 class="ng-farmley-blog-hero__title"><?php echo esc_html( $title ); ?></h1>
					<p class="ng-farmley-blog-hero__subtitle"><?php echo esc_html( $copy ); ?></p>
				</div>
				<div class="ng-farmley-blog-hero__stats" aria-label="<?php esc_attr_e( 'Blog highlights', 'nuttergood' ); ?>">
					<?php foreach ( $config['stats'] as $stat ) : ?>
						<div class="ng-farmley-blog-hero__stat">
							<strong><?php echo esc_html( $stat['value'] ); ?></strong>
							<span><?php echo esc_html( $stat['label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="ng-farmley-blog-hero__filters" role="tablist" aria-label="<?php esc_attr_e( 'Filter stories by topic', 'nuttergood' ); ?>">
				<?php foreach ( $config['filters'] as $i => $filter ) : ?>
					<button
						type="button"
						class="ng-farmley-blog-filter<?php echo 0 === $i ? ' is-active' : ''; ?>"
						data-ng-blog-filter="<?php echo esc_attr( $filter['slug'] ); ?>"
						role="tab"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
					>
						<?php echo esc_html( $filter['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
	add_action( 'greenpath_action_before_blog_loop', 'nuttergood_farmley_render_blog_archive_hero', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_render_blog_card_meta' ) ) {
	function nuttergood_farmley_render_blog_card_meta() {
		$meta = nuttergood_farmley_get_blog_post_meta();

		if ( is_single() ) {
			if ( ! empty( $meta['kicker'] ) ) {
				echo '<p class="ng-farmley-blog-single__kicker">' . esc_html( $meta['kicker'] ) . '</p>';
			}
			return;
		}
		?>
		<div class="ng-farmley-blog-card__meta">
			<?php if ( ! empty( $meta['read_time'] ) ) : ?>
				<span class="ng-farmley-blog-card__read-time"><?php echo esc_html( $meta['read_time'] ); ?></span>
			<?php endif; ?>
		</div>
		<?php
		if ( ! empty( $meta['kicker'] ) ) {
			echo '<p class="ng-farmley-blog-card__kicker">' . esc_html( $meta['kicker'] ) . '</p>';
		}
	}
}

if ( ! function_exists( 'nuttergood_farmley_blog_card_media_badge' ) ) {
	function nuttergood_farmley_blog_card_media_badge() {
		if ( is_single() ) {
			$meta = nuttergood_farmley_get_blog_post_meta();
			echo '<div class="ng-farmley-blog-single__media-badges">';
			if ( ! empty( $meta['category'] ) ) {
				echo '<span class="ng-farmley-blog-single__badge">' . esc_html( $meta['category'] ) . '</span>';
			}
			if ( ! empty( $meta['read_time'] ) ) {
				echo '<span class="ng-farmley-blog-single__badge ng-farmley-blog-single__badge--muted">' . esc_html( $meta['read_time'] ) . '</span>';
			}
			echo '</div>';
			return;
		}

		$meta = nuttergood_farmley_get_blog_post_meta();
		if ( empty( $meta['category'] ) ) {
			return;
		}
		echo '<span class="ng-farmley-blog-card__media-badge">' . esc_html( $meta['category'] ) . '</span>';
	}
	add_action( 'greenpath_action_after_post_thumbnail_image', 'nuttergood_farmley_blog_card_media_badge', 12 );
}

if ( ! function_exists( 'nuttergood_farmley_blog_post_thumbnail_fallback' ) ) {
	function nuttergood_farmley_blog_post_thumbnail_fallback( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		if ( $html || ! nuttergood_farmley_is_blog_context() ) {
			return $html;
		}

		$meta = nuttergood_farmley_get_blog_post_meta( $post_id );
		if ( empty( $meta['image'] ) ) {
			return $html;
		}

		$alt = the_title_attribute( array( 'echo' => false, 'post' => $post_id ) );

		return sprintf(
			'<img src="%1$s" alt="%2$s" class="attachment-%3$s size-%3$s wp-post-image" loading="lazy" decoding="async" />',
			esc_url( $meta['image'] ),
			esc_attr( $alt ),
			esc_attr( is_string( $size ) ? $size : 'full' )
		);
	}
	add_filter( 'post_thumbnail_html', 'nuttergood_farmley_blog_post_thumbnail_fallback', 20, 5 );
}

if ( ! function_exists( 'nuttergood_farmley_blog_excerpt' ) ) {
	function nuttergood_farmley_blog_excerpt( $excerpt ) {
		if ( is_single() || is_admin() ) {
			return $excerpt;
		}

		$meta = nuttergood_farmley_get_blog_post_meta();
		if ( ! empty( $meta['excerpt'] ) ) {
			return $meta['excerpt'];
		}

		return $excerpt;
	}
	add_filter( 'get_the_excerpt', 'nuttergood_farmley_blog_excerpt', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_render_blog_sidebar' ) ) {
	function nuttergood_farmley_render_blog_sidebar() {
		if ( ! nuttergood_farmley_is_blog_context() ) {
			return;
		}

		$config = nuttergood_farmley_blog_page_config();
		$posts  = get_posts(
			array(
				'post_type'      => 'post',
				'posts_per_page' => 4,
				'post_status'    => 'publish',
			)
		);
		$shop   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		?>
		<div class="ng-farmley-blog-sidebar">
			<div class="ng-farmley-blog-sidebar__widget ng-farmley-blog-sidebar__widget--about">
				<h3 class="ng-farmley-blog-sidebar__title"><?php esc_html_e( 'About the journal', 'nuttergood' ); ?></h3>
				<p><?php echo esc_html( $config['sidebar_about'] ); ?></p>
			</div>

			<div class="ng-farmley-blog-sidebar__widget">
				<h3 class="ng-farmley-blog-sidebar__title"><?php esc_html_e( 'Browse topics', 'nuttergood' ); ?></h3>
				<div class="ng-farmley-blog-sidebar__tags">
					<?php foreach ( $config['filters'] as $filter ) : ?>
						<?php if ( 'all' === $filter['slug'] ) { continue; } ?>
						<button type="button" class="ng-farmley-blog-sidebar__tag" data-ng-blog-filter="<?php echo esc_attr( $filter['slug'] ); ?>">
							<?php echo esc_html( $filter['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="ng-farmley-blog-sidebar__widget">
				<h3 class="ng-farmley-blog-sidebar__title"><?php esc_html_e( 'Popular reads', 'nuttergood' ); ?></h3>
				<ul class="ng-farmley-blog-sidebar__list">
					<?php foreach ( $posts as $i => $post ) : ?>
						<?php $meta = nuttergood_farmley_get_blog_post_meta( $post->ID ); ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
								<span class="ng-farmley-blog-sidebar__list-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="ng-farmley-blog-sidebar__list-text">
									<strong><?php echo esc_html( get_the_title( $post ) ); ?></strong>
									<em><?php echo esc_html( $meta['read_time'] ?? '' ); ?></em>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="ng-farmley-blog-sidebar__widget ng-farmley-blog-sidebar__widget--cta">
				<span class="ng-farmley-blog-sidebar__cta-label"><?php esc_html_e( 'Ready to snack better?', 'nuttergood' ); ?></span>
				<h3><?php esc_html_e( 'Shop premium dry fruits & mixes', 'nuttergood' ); ?></h3>
				<p><?php esc_html_e( 'Handpicked almonds, trail mixes, chips, and mouth fresheners delivered across Hyderabad.', 'nuttergood' ); ?></p>
				<a class="ng-farmley-blog-sidebar__cta-btn" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Explore shop', 'nuttergood' ); ?></a>
			</div>
		</div>
		<?php
	}
	add_action( 'greenpath_action_before_page_sidebar', 'nuttergood_farmley_render_blog_sidebar', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_render_blog_related' ) ) {
	function nuttergood_farmley_render_blog_related() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$current_id = get_the_ID();
		$posts      = get_posts(
			array(
				'post_type'      => 'post',
				'posts_per_page' => 3,
				'post_status'    => 'publish',
				'post__not_in'   => array( $current_id ),
			)
		);

		if ( empty( $posts ) ) {
			return;
		}
		?>
		<section class="ng-farmley-blog-related" aria-label="<?php esc_attr_e( 'Related articles', 'nuttergood' ); ?>">
			<div class="ng-farmley-blog-related__head">
				<p class="ng-farmley-blog-related__eyebrow"><?php esc_html_e( 'Keep reading', 'nuttergood' ); ?></p>
				<h2><?php esc_html_e( 'More stories you may like', 'nuttergood' ); ?></h2>
			</div>
			<div class="ng-farmley-blog-related__grid">
				<?php foreach ( $posts as $post ) : ?>
					<?php
					$meta  = nuttergood_farmley_get_blog_post_meta( $post->ID );
					$image = get_the_post_thumbnail_url( $post, 'medium_large' );
					if ( ! $image && ! empty( $meta['image'] ) ) {
						$image = $meta['image'];
					}
					?>
					<article class="ng-farmley-blog-related__card">
						<a class="ng-farmley-blog-related__media" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
							<?php if ( $image ) : ?>
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $post ) ); ?>" loading="lazy" decoding="async" />
							<?php endif; ?>
						</a>
						<div class="ng-farmley-blog-related__body">
							<?php if ( ! empty( $meta['category'] ) ) : ?>
								<span class="ng-farmley-blog-related__category"><?php echo esc_html( $meta['category'] ); ?></span>
							<?php endif; ?>
							<h3><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h3>
							<p><?php echo esc_html( $meta['excerpt'] ?? '' ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
	add_action( 'greenpath_action_after_blog_post_item', 'nuttergood_farmley_render_blog_related', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_render_blog_enhancement' ) ) {
	function nuttergood_farmley_render_blog_enhancement( $data ) {
		ob_start();
		?>
		<div class="ng-blog-enhancement">
			<div class="ng-blog-enhancement__takeaways">
				<h2><?php esc_html_e( 'Quick takeaways', 'nuttergood' ); ?></h2>
				<ul>
					<?php foreach ( $data['takeaways'] as $takeaway ) : ?>
						<li><?php echo esc_html( $takeaway ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="ng-blog-enhancement__sections">
				<?php foreach ( $data['sections'] as $section ) : ?>
					<section class="ng-blog-enhancement__section">
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<p><?php echo esc_html( $section['body'] ); ?></p>
					</section>
				<?php endforeach; ?>
			</div>

			<div class="ng-blog-enhancement__checklist">
				<div>
					<span><?php esc_html_e( 'Snack smarter', 'nuttergood' ); ?></span>
					<h2><?php esc_html_e( 'Before you choose, check these basics', 'nuttergood' ); ?></h2>
				</div>
				<ul>
					<?php foreach ( $data['checklist'] as $item ) : ?>
						<li><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'nuttergood_farmley_blog_single_lede' ) ) {
	function nuttergood_farmley_blog_single_lede( $content ) {
		if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$meta = nuttergood_farmley_get_blog_post_meta();
		if ( empty( $meta['intro'] ) ) {
			return $content;
		}

		$lede = '<p class="ng-farmley-blog-single__lede">' . esc_html( $meta['intro'] ) . '</p>';

		return $lede . $content;
	}

	add_filter( 'the_content', 'nuttergood_farmley_blog_single_lede', 15 );
}

if ( ! function_exists( 'nuttergood_farmley_enhance_blog_content' ) ) {
	function nuttergood_farmley_enhance_blog_content( $content ) {
		if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$data = nuttergood_farmley_blog_article_data();
		$slug = get_post_field( 'post_name', get_the_ID() );

		if ( empty( $data[ $slug ] ) ) {
			return $content;
		}

		return $content . nuttergood_farmley_render_blog_enhancement( $data[ $slug ] );
	}

	add_filter( 'the_content', 'nuttergood_farmley_enhance_blog_content', 20 );
}