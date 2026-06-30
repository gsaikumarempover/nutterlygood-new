<?php

define( 'GREENPATH_CORE_REVIEWS_MAX_RATING', 5 );
define( 'GREENPATH_CORE_REVIEWS_POINTS_SCALE', 2 );

if ( ! function_exists( 'greenpath_core_rating_posts_types' ) ) {
	/**
	 * Function for defining post types that can be reviewed
	 *
	 * @return array
	 */
	function greenpath_core_rating_posts_types() {
		return apply_filters( 'greenpath_core_filter_rating_post_types', array() );
	}
}

if ( ! function_exists( 'greenpath_core_rating_criteria' ) ) {
	/**
	 * Function for defining post types that can be reviewed
	 *
	 * @return array
	 */
	function greenpath_core_rating_criteria( $key = 'qodef_global_rating' ) {
		$rating_criteria   = array();
		$rating_criteria[] = array(
			'key'   => $key,
			'label' => esc_html__( 'Rating', 'greenpath-core' ),
			'show'  => true,
		);

		return apply_filters( 'greenpath_core_filter_rating_criteria', $rating_criteria );
	}
}

if ( ! function_exists( 'greenpath_core_taxonomy_rating_array' ) ) {
	/**
	 * Function for generating taxonomy array
	 *
	 * @param string $taxonomy_name
	 *
	 * @return array
	 */
	function greenpath_core_taxonomy_rating_array( $taxonomy_name ) {
		//Get the necessary data about user-defined review taxonomy
		global $wpdb;

		if ( qode_framework_is_installed( 'wpml' ) ) {
			$lang               = ICL_LANGUAGE_CODE;
			$wpml_taxonomy_name = 'tax_' . $taxonomy_name;
			$sql                = "SELECT t.term_id AS 'id',
					                       t.slug AS 'key',
					                       t.name AS 'label'
									    FROM {$wpdb->prefix}terms t
									    LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON tt.term_id = t.term_id
									    LEFT JOIN {$wpdb->prefix}icl_translations icl_t ON icl_t.element_id = t.term_id
									    WHERE icl_t.element_type = '$wpml_taxonomy_name'
									    AND icl_t.language_code='$lang'
									    ORDER BY name ASC";
		} else {
			$sql = "SELECT t.term_id AS 'id',
	                       t.slug AS 'key',
	                       t.name AS 'label'
	                    FROM {$wpdb->prefix}terms t
	                    LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON tt.term_id = t.term_id
	                    WHERE tt.taxonomy = '$taxonomy_name'
	                    ORDER BY name ASC";
		}

		$review_criteria = $wpdb->get_results( $sql, 'ARRAY_A' );

		$final_criteria = array();

		if ( ! empty( $review_criteria ) ) {
			$taxonomy_name_meta = str_replace( '-', '_', $taxonomy_name );
			foreach ( $review_criteria as $review_criterion ) {
				$temp_criterion          = (array) $review_criterion;
				$term_meta               = get_term_meta( $temp_criterion['id'] );
				$is_reviews_enabled      = ( isset( $term_meta[ $taxonomy_name_meta . '_show' ][0] ) && 'no' != $term_meta[ $taxonomy_name_meta . '_show' ][0] );
				$temp_criterion['show']  = $is_reviews_enabled;
				$temp_criterion['order'] = isset( $term_meta[ $taxonomy_name_meta . '_order' ][0] ) ? (int) $term_meta[ $taxonomy_name_meta . '_order' ][0] : PHP_INT_MAX;

				if ( $is_reviews_enabled ) {
					$final_criteria[] = $temp_criterion;
				}
			}

			for ( $i = 0; $i < count( $final_criteria ) - 1; $i ++ ) {
				for ( $j = $i + 1; $j < count( $final_criteria ); $j ++ ) {
					if ( $final_criteria[ $i ]['order'] > $final_criteria[ $j ]['order'] ) {
						$temp                 = $final_criteria[ $i ];
						$final_criteria[ $i ] = $final_criteria[ $j ];
						$final_criteria[ $j ] = $temp;
					}
				}
			}
		}

		return $final_criteria;
	}
}

if ( ! function_exists( 'greenpath_core_extend_comment_meta_box' ) ) {
	/**
	 * Function for adding comment meta boxes and its callback in admin
	 */
	function greenpath_core_extend_comment_meta_box() {
		add_meta_box(
			'title',
			esc_html__( 'Comment - Reviews', 'greenpath-core' ),
			'greenpath_core_extend_comment_meta_box_callback',
			'comment',
			'normal',
			'high'
		);
	}

	add_action( 'add_meta_boxes_comment', 'greenpath_core_extend_comment_meta_box' );
}

if ( ! function_exists( 'greenpath_core_extend_comment_meta_box_callback' ) ) {
	/**
	 * Function that extend global comments field with additional template
	 *
	 * @param object $comment
	 */
	function greenpath_core_extend_comment_meta_box_callback( $comment ) {
		$post_types = greenpath_core_rating_posts_types();

		if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
			foreach ( $post_types as $post_type ) {
				if ( $comment->post_type === $post_type ) {
					wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );

					$rating_criteria = greenpath_core_rating_criteria();
					foreach ( $rating_criteria as $criteria ) {
						$star_params           = array();
						$star_params['label']  = $criteria['label'];
						$star_params['key']    = $criteria['key'];
						$star_params['rating'] = get_comment_meta( $comment->comment_ID, $criteria['key'], true );

						echo greenpath_core_get_template_part( 'reviews', 'templates/admin/stars-field', '', $star_params );
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'greenpath_core_extend_comment_edit_metafields' ) ) {
	/**
	 * Function that is triggered when comment is edited
	 *
	 * @param int $comment_id
	 */
	function greenpath_core_extend_comment_edit_metafields( $comment_id ) {
		if ( ( ! isset( $_POST['extend_comment_update'] ) || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ) ) {
			return;
		}

		if ( isset( $_POST['qodef_comment_title'] ) && '' !== $_POST['qodef_comment_title'] ) :
			$title = wp_filter_nohtml_kses( $_POST['qodef_comment_title'] );
			update_comment_meta( $comment_id, 'qodef_comment_title', $title );
		else :
			delete_comment_meta( $comment_id, 'qodef_comment_title' );
		endif;

		$rating_criteria = greenpath_core_rating_criteria();
		foreach ( $rating_criteria as $criteria ) {
			if ( isset( $_POST[ $criteria['key'] ] ) && '' !== $_POST[ $criteria['key'] ] ) :
				$rating = wp_filter_nohtml_kses( $_POST[ $criteria['key'] ] );
				update_comment_meta( $comment_id, $criteria['key'], $rating );
			else :
				delete_comment_meta( $comment_id, $criteria['key'] );
			endif;
		}
	}

	add_action( 'edit_comment', 'greenpath_core_extend_comment_edit_metafields' );
}

if ( ! function_exists( 'greenpath_core_extend_comment_save_metafields' ) ) {
	/**
	 * Function that is triggered when comment is saved
	 *
	 * @param int $comment_id
	 */
	function greenpath_core_extend_comment_save_metafields( $comment_id ) {

		if ( isset( $_POST['qodef_comment_title'] ) && '' !== $_POST['qodef_comment_title'] ) {
			$title = wp_filter_nohtml_kses( $_POST['qodef_comment_title'] );
			add_comment_meta( $comment_id, 'qodef_comment_title', $title );
		}

		$rating_criteria = greenpath_core_rating_criteria();
		foreach ( $rating_criteria as $criteria ) {
			if ( isset( $_POST[ $criteria['key'] ] ) && '' !== $_POST[ $criteria['key'] ] ) {
				$rating = wp_filter_nohtml_kses( $_POST[ $criteria['key'] ] );
				add_comment_meta( $comment_id, $criteria['key'], $rating );
			}
		}
	}

	add_action( 'comment_post', 'greenpath_core_extend_comment_save_metafields' );
}

if ( ! function_exists( 'greenpath_core_extend_comment_preprocess_metafields' ) ) {
	/**
	 * Function that is triggered before comment is saved
	 *
	 * @param array $commentdata
	 *
	 * @return array
	 */
	function greenpath_core_extend_comment_preprocess_metafields( $commentdata ) {
		$post_types = greenpath_core_rating_posts_types();

		if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
			foreach ( $post_types as $post_type ) {
				if ( is_singular( $post_type ) ) {
					$rating_criteria = greenpath_core_rating_criteria();
					foreach ( $rating_criteria as $criteria ) {
						if ( ! isset( $_POST[ $criteria['key'] ] ) ) {
							wp_die( esc_html__( 'Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.', 'greenpath-core' ) );
							break;
						}
					}
				}
			}
		}

		return $commentdata;
	}

	add_filter( 'preprocess_comment', 'greenpath_core_extend_comment_preprocess_metafields' );
}

if ( ! function_exists( 'greenpath_core_comment_additional_fields' ) ) {
	/**
	 * Function that adds items to Post Comments section
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function greenpath_core_comment_additional_fields( $args ) {
		$post_types = greenpath_core_rating_posts_types();

		if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
			foreach ( $post_types as $post_type ) {
				if ( is_singular( $post_type ) ) {
					$textarea = '';

					$rating_criteria = greenpath_core_rating_criteria();

					if ( is_array( $rating_criteria ) ) {
						$textarea .= '<div class="qodef-review-rating">';
						foreach ( $rating_criteria as $criteria ) {
							$star_params          = array();
							$star_params['label'] = $criteria['label'];
							$star_params['key']   = $criteria['key'];

							$textarea .= greenpath_core_get_template_part( 'reviews', 'templates/front-input/stars-field', '', $star_params );
						}
						$textarea .= '</div>';
					}

					$textarea .= greenpath_core_get_template_part( 'reviews', 'templates/front-input/text-field' );

					$args['comment_field'] = $textarea;
				}
			}
		}

		return $args;
	}

	add_filter( 'greenpath_filter_comment_form_args', 'greenpath_core_comment_additional_fields' );
}

if ( ! function_exists( 'greenpath_core_override_comments_list_callback' ) ) {
	/**
	 * Function that through theme filter renders listed comments on single post, and it's callback
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function greenpath_core_override_comments_list_callback( $args ) {
		$post_types = greenpath_core_rating_posts_types();

		if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
			foreach ( $post_types as $post_type ) {
				if ( is_singular( $post_type ) ) {
					$args['callback'] = 'greenpath_core_list_reviews';
				}
			}
		}

		return $args;
	}

	add_filter( 'greenpath_filter_comments_list_template_callback', 'greenpath_core_override_comments_list_callback' );
}

if ( ! function_exists( 'greenpath_core_list_reviews' ) ) {
	/**
	 * Function that adds list review items
	 *
	 * @param object $comment
	 * @param array $args
	 * @param int $depth
	 *
	 * @return string that contains html content
	 */
	function greenpath_core_list_reviews( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		global $post;

		$is_pingback_comment = 'pingback' === $comment->comment_type;
		$is_author_comment   = $post->post_author == $comment->user_id;

		$comment_class = 'qodef-comment clearfix';

		if ( $is_author_comment ) {
			$comment_class .= ' qodef-post-author-comment';
		}

		$params                        = array();
		$params['comment']             = $comment;
		$params['comment_class']       = $comment_class;
		$params['is_pingback_comment'] = $is_pingback_comment;
		$params['review_title']        = get_comment_meta( $comment->comment_ID, 'qodef_comment_title', true );
		$params['rating_criteria']     = greenpath_core_rating_criteria();

		echo greenpath_core_get_template_part( 'reviews', 'templates/front-list/item-holder', '', $params );
	}
}

if ( ! function_exists( 'greenpath_core_list_review_details' ) ) {
	/**
	 * Functions for getting review details for rendering above comments list
	 *
	 * @param string $template
	 * @param string $key
	 * @param array $unset_params
	 * @param string $title_tag
	 *
	 * @return string that contains html content
	 */
	function greenpath_core_list_review_details( $template = 'simple', $key = 'qodef_global_rating', $unset_params = array(), $title_tag = 'h4' ) {
		$params                  = array();
		$params['title_tag']     = $title_tag;
		$params['rating_number'] = greenpath_core_post_number_of_ratings();
		$params['rating_label']  = 1 === greenpath_core_post_number_of_ratings() ? esc_html__( 'Review', 'greenpath-core' ) : esc_html__( 'Reviews', 'greenpath-core' );
		$params['post_ratings']  = greenpath_core_post_ratings( '', $key );
		$params['width_title']   = true;

		if ( ! empty( $unset_params ) ) {
			foreach ( $unset_params as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $value_key => $new_value ) {
						unset( $params[ $key ][ $value_key ][ $new_value ] );
					}
				} else {
					unset( $params[ $value ] );
				}
			}
		}

		return greenpath_core_get_template_part( 'reviews', 'templates/front-list/details', $template, $params );
	}
}

if ( ! function_exists( 'greenpath_core_post_ratings' ) ) {
	/**
	 * Functions for getting approved comments and their values for displaying info
	 *
	 * @param int|string $id
	 *
	 * @return array
	 */
	function greenpath_core_post_ratings( $id = '', $key = 'qodef_global_rating' ) {
		$id            = ! empty( $id ) ? $id : get_the_ID();
		$comment_array = get_approved_comments( $id );

		$rating_criteria = greenpath_core_rating_criteria( $key );
		foreach ( $rating_criteria as $key => $criteria ) {
			$marks = array(
				'5' => 0,
				'4' => 0,
				'3' => 0,
				'2' => 0,
				'1' => 0,
			);

			$count = 0;
			foreach ( $comment_array as $comment ) {
				$rating = get_comment_meta( $comment->comment_ID, $criteria['key'], true );

				if ( '' != $rating && 0 != $rating ) {
					$marks[ $rating ] = $marks[ $rating ] + 1;
					$count ++;
				}
			}

			$criteria['marks'] = $marks;
			$criteria['count'] = $count;

			$rating_criteria[ $key ] = $criteria;
		}

		return $rating_criteria;
	}
}

if ( ! function_exists( 'greenpath_core_post_number_of_ratings' ) ) {
	/**
	 * Calculation functions
	 *
	 * @param int|string $id
	 *
	 * @return int
	 */
	function greenpath_core_post_number_of_ratings( $id = '' ) {
		$id            = ! empty( $id ) ? $id : get_the_ID();
		$comment_array = get_approved_comments( $id );
		$count         = ! empty( $comment_array ) ? count( $comment_array ) : 0;

		return $count;
	}
}

if ( ! function_exists( 'greenpath_core_post_average_rating' ) ) {
	/**
	 * Function that get average post rating
	 *
	 * @param array $criteria
	 *
	 * @return int
	 */
	function greenpath_core_post_average_rating( $criteria ) {
		$sum     = 0;
		$ratings = $criteria['marks'];
		$count   = $criteria['count'];
		foreach ( $ratings as $rating => $value ) {
			$sum = $sum + $rating * $value;
		}

		$average = 0 == $count ? 0 : round( $sum / $count );

		return $average;
	}
}

if ( ! function_exists( 'greenpath_core_post_average_rating_per_criteria' ) ) {
	/**
	 * Function that get average post rating per criteria
	 *
	 * @param array $criteria
	 *
	 * @return int
	 */
	function greenpath_core_post_average_rating_per_criteria( $criteria ) {
		$average = greenpath_core_post_average_rating( $criteria );
		$average = $average / GREENPATH_CORE_REVIEWS_MAX_RATING * 100;

		return $average;
	}
}

if ( ! function_exists( 'greenpath_core_get_total_average_rating' ) ) {
	/**
	 * Function that get total average rating
	 *
	 * @param array $criteria_array
	 *
	 * @return int
	 */
	function greenpath_core_get_total_average_rating( $criteria_array ) {
		$sum = 0;

		if ( is_array( $criteria_array ) && count( $criteria_array ) ) {
			foreach ( $criteria_array as $criteria ) {
				$sum += greenpath_core_post_average_rating( $criteria );
			}

			return $sum / count( $criteria_array );
		}

		return $sum;
	}
}

if ( ! function_exists( 'greenpath_core_reviews_format_rating_output' ) ) {
	/**
	 * Formatting functions
	 *
	 * @param int $rating
	 *
	 * @return float
	 */
	function greenpath_core_reviews_format_rating_output( $rating ) {
		return floor( $rating * GREENPATH_CORE_REVIEWS_POINTS_SCALE ) . '.' . round( $rating * GREENPATH_CORE_REVIEWS_POINTS_SCALE * 10 ) % 10;
	}
}

if ( ! function_exists( 'greenpath_core_reviews_get_icon_list' ) ) {
	/**
	 * Function that return reviews icons list
	 *
	 * @return array
	 */
	function greenpath_core_reviews_get_icon_list() {
		return array(
			'<span class="lnr lnr-sad"></span>',
			'<span class="lnr lnr-neutral"></span>',
			'<span class="lnr lnr-smile"></span>',
		);
	}
}

if ( ! function_exists( 'greenpath_core_reviews_get_description_list' ) ) {
	/**
	 * Function that return reviews description list
	 *
	 * @return array
	 */
	function greenpath_core_reviews_get_description_list() {
		return array(
			esc_html__( 'Poor', 'greenpath-core' ),
			esc_html__( 'Good', 'greenpath-core' ),
			esc_html__( 'Superb', 'greenpath-core' ),
		);
	}
}

if ( ! function_exists( 'greenpath_core_reviews_get_icon_for_rating' ) ) {
	/**
	 * Function that return reviews icon
	 *
	 * @param int $rating
	 *
	 * @return string
	 */
	function greenpath_core_reviews_get_icon_for_rating( $rating ) {
		if ( ! $rating ) {
			return '';
		}

		$icons = greenpath_core_reviews_get_icon_list();
		$delta = GREENPATH_CORE_REVIEWS_MAX_RATING / count( $icons );

		return $icons[ ceil( $rating / $delta ) - 1 ];
	}
}

if ( ! function_exists( 'greenpath_core_reviews_get_description_for_rating' ) ) {
	/**
	 * Function that return reviews description info
	 *
	 * @param int $rating
	 *
	 * @return string
	 */
	function greenpath_core_reviews_get_description_for_rating( $rating ) {
		if ( ! $rating ) {
			return '';
		}

		$terms = greenpath_core_reviews_get_description_list();
		$delta = GREENPATH_CORE_REVIEWS_MAX_RATING / count( $terms );

		return $terms[ ceil( $rating / $delta ) - 1 ];
	}
}

if ( ! function_exists( 'greenpath_core_reviews_get_rating_html' ) ) {
	/**
	 * Function that override ratings templates
	 *
	 * @param string $html - contains html content
	 * @param float $rating
	 * @param int $count - total number of ratings
	 *
	 * @return string
	 */
	function greenpath_core_reviews_get_rating_html( $html, $rating, $count ) {
		if ( ! empty( $rating ) ) {
			$html  = '<div class="qodef-comments-ratings qodef-m"><div class="qodef-m-inner">';
			$html .= '<div class="qodef-m-star qodef--initial">';
			for ( $i = 0; $i < 5; $i ++ ) {
				$html .= greenpath_core_get_svg_icon( 'star' );
			}
			$html .= '</div>';
			$html .= '<div class="qodef-m-star qodef--active" style="width:' . ( 20 * $rating + ( ceil( $rating ) - 1 ) * 4 ) . 'px">'; // 20 is star width in px and 12 is space between stars in px
			for ( $i = 0; $i < 5; $i ++ ) {
				$html .= greenpath_core_get_svg_icon( 'star' );
			}
			$html .= '</div>';
			$html .= '</div></div>';
		}

		return $html;
	}
}
