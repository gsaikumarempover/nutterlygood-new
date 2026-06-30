<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Privacy_Wishlist' ) ) {
	class Qode_Wishlist_For_WooCommerce_Privacy_Wishlist {
		private $plugin_name;
		private static $instance;

		public function get_plugin_name() {
			return $this->plugin_name;
		}

		public function set_plugin_name( $plugin_name ) {
			$this->plugin_name = $plugin_name;
		}

		public function __construct() {
			$this->set_plugin_name( 'QODE Wishlist for WooCommerce' );

			// Let's initialize the privacy.
			add_filter( 'qode_wishlist_for_woocommerce_filter_privacy_policy_guide_content', array( $this, 'add_message_in_section' ), 10, 2 );

			// Set up wishlist data exporter.
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

			// Set up wishlist data eraser.
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Privacy_Wishlist
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add message in a specific section.
		 *
		 * @param string $html    The HTML of the section.
		 * @param string $section The section.
		 *
		 * @return string
		 */
		public function add_message_in_section( $html, $section ) {
			$message = $this->get_privacy_message( $section );

			if ( $message ) {
				$html .= "<p class='privacy-policy-tutorial'><strong>{$this->get_plugin_name()}</strong></p>";
				$html .= $message;
			}

			return $html;
		}

		/**
		 * Retrieves privacy policy message in a specific section.
		 *
		 * @param string $section Section of the message to retrieve.
		 *
		 * @return string
		 */
		public function get_privacy_message( $section ) {
			$content = '';

			switch ( $section ) {
				case 'collect_and_store':
					$content = sprintf(
						'<p>%1$s</p><ul><li>%2$s</li><li>%3$s</li></ul><p>%4$s</p>',
						esc_html__( 'When you visit and interact with our website, we track:', 'qode-wishlist-for-woocommerce' ),
						esc_html__( 'Products you add to wishlists - this is done in order to present you and other visitors your favorite products, and to create targeted email campaigns.', 'qode-wishlist-for-woocommerce' ),
						esc_html__( 'Wishlists created by you - we\'ll monitor wishlists you create and make them available to the store personnel.', 'qode-wishlist-for-woocommerce' ),
						esc_html__( 'We also use cookies to follow wishlist contents while you browse our website.', 'qode-wishlist-for-woocommerce' )
					);
					break;
				case 'has_access':
					$content = sprintf(
						'<p>%1$s</p><ul><li>%2$s</li></ul><p>%3$s</p>',
						esc_html__( 'Our team members have access to the information you provide us with.', 'qode-wishlist-for-woocommerce' ),
						esc_html__( 'For instance, site Administrators and Shop Managers can view the general wishlist info, like added products, addition dates, wishlist names and their privacy settings.', 'qode-wishlist-for-woocommerce' ),
						esc_html__( 'Our team has access to this type of data in order to offer you better deals and promotions for the products you are interested in.', 'qode-wishlist-for-woocommerce' )
					);
					break;
				case 'share':
				case 'payments':
				default:
					break;
			}

			return $content;
		}

		/**
		 * Register exporters
		 *
		 * @param array $exporters
		 *
		 * @return array
		 */
		public function register_exporter( $exporters ) {
			$exporters['qwfw_exporter'] = array(
				'exporter_friendly_name' => esc_html__( 'Customer Wishlists', 'qode-wishlist-for-woocommerce' ),
				'callback'               => array( $this, 'wishlist_data_exporter' ),
			);

			return $exporters;
		}

		/**
		 * Register eraser
		 *
		 * @param array $erasers
		 *
		 * @return array
		 */
		public function register_eraser( $erasers ) {
			$erasers['qwfw_eraser'] = array(
				'eraser_friendly_name' => esc_html__( 'Customer Wishlists', 'qode-wishlist-for-woocommerce' ),
				'callback'             => array( $this, 'wishlist_data_eraser' ),
			);

			return $erasers;
		}

		/**
		 * Export user wishlists (only available for authenticated users' wishlist)
		 *
		 * @param string $email_address Email of the users that requested export.
		 * @param int    $page Current page processed.
		 *
		 * @return array
		 */
		public function wishlist_data_exporter( $email_address, $page ) {
			$done           = true;
			$page           = (int) $page;
			$offset         = 10 * ( $page - 1 );
			$user           = get_user_by( 'email', $email_address );
			$data_to_export = array();

			if ( $user instanceof WP_User ) {
				$wishlists = qode_wishlist_for_woocommerce_get_privacy_user_wishlist_items(
					array(
						'limit'   => 10,
						'offset'  => $offset,
						'user_id' => $user->ID,
					)
				);

				if ( ! empty( $wishlists ) ) {
					foreach ( $wishlists as $wishlist_key => $wishlist ) {
						$data_to_export[] = array(
							'group_id'    => 'qwfw_wishlist',
							'group_label' => esc_html__( 'Wishlists', 'qode-wishlist-for-woocommerce' ),
							'item_id'     => 'qwfw-wishlist-' . $wishlist_key,
							'data'        => $this->get_wishlist_personal_data( $wishlist ),
						);
					}

					$done = 10 > count( $wishlists );
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Retrieves data to export for each user's wishlist
		 *
		 * @param array $wishlist
		 *
		 * @return array
		 */
		protected function get_wishlist_personal_data( $wishlist ) {
			$personal_data = array();

			$props_to_export = array(
				'token'        => esc_html__( 'Token', 'qode-wishlist-for-woocommerce' ),
				'wishlist_url' => esc_html__( 'Wishlist URL', 'qode-wishlist-for-woocommerce' ),
				'table_title'  => esc_html__( 'Title', 'qode-wishlist-for-woocommerce' ),
				'date_created' => _x( 'Created on', 'date when wishlist was created', 'qode-wishlist-for-woocommerce' ),
				'visibility'   => esc_html__( 'Visibility', 'qode-wishlist-for-woocommerce' ),
				'items'        => esc_html__( 'Items added', 'qode-wishlist-for-woocommerce' ),
			);

			foreach ( $props_to_export as $prop => $name ) {
				$value = '';

				switch ( $prop ) {
					case 'items':
						$item_names = array();
						$items      = $wishlist['items'] ?? array();

						foreach ( $items as $item ) {
							$product = wc_get_product( $item['product_id'] );

							if ( ! $product ) {
								continue;
							}

							$item_name = sprintf(
								'%s %s %s %s',
								wp_kses_post( $product->get_name() ),
								esc_html__( 'x', 'qode-wishlist-for-woocommerce' ),
								esc_attr( $item['quantity'] ),
								// translators: date when item is added into wishlist.
								isset( $item['date_added'] ) ? sprintf( esc_html__( '(on: %s)', 'qode-wishlist-for-woocommerce' ), $item['date_added'] ) : ''
							);

							$item_names[] = $item_name;
						}

						$value = implode( ', ', $item_names );
						break;
					case 'wishlist_url':
						$wishlist_url_args = array(
							'view' => $wishlist['token'],
						);

						if ( 'default' !== $wishlist['table_key'] ) {
							$wishlist_url_args['table'] = $wishlist['table_key'];
						}

						$wishlist_url = qode_wishlist_for_woocommerce_get_wishlist_page_url_with_args( $wishlist_url_args );

						$value = sprintf( '<a href="%1$s">%1$s</a>', $wishlist_url );
						break;
					default:
						if ( isset( $wishlist[ $prop ] ) ) {
							$value = $wishlist[ $prop ];
						}
						break;
				}

				if ( $value ) {
					$personal_data[] = array(
						'name'  => $name,
						'value' => $value,
					);
				}
			}

			return $personal_data;
		}

		/**
		 * Deletes user wishlists (only available for authenticated users' wishlist)
		 *
		 * @param string $email_address Email of the users that requested export.
		 * @param int    $page Current page processed.
		 *
		 * @return array Result of the operation
		 */
		public function wishlist_data_eraser( $email_address, $page ) {
			$page     = (int) $page;
			$offset   = 10 * ( $page - 1 );
			$user     = get_user_by( 'email', $email_address );
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			if ( ! $user instanceof WP_User ) {
				return $response;
			}

			$wishlists = qode_wishlist_for_woocommerce_get_privacy_user_wishlist_items(
				array(
					'limit'   => 10,
					'offset'  => $offset,
					'user_id' => $user->ID,
				)
			);

			if ( ! empty( $wishlists ) ) {
				foreach ( $wishlists as $wishlist ) {

					if ( apply_filters( 'qode_wishlist_for_woocommerce_filter_privacy_erase_wishlist_personal_data', true, $wishlist ) ) {
						qode_wishlist_for_woocommerce_remove_wishlist_item_db( $wishlist['token'], $wishlist['table_key'] );

						// translators: %s wishlist unique token.
						$response['messages'][]    = sprintf( esc_html__( 'Removed wishlist "%s".', 'qode-wishlist-for-woocommerce' ), $wishlist['table_title'] );
						$response['items_removed'] = true;
					} else {
						// translators: %s wishlist unique token.
						$response['messages'][]     = sprintf( esc_html__( 'Wishlist "%s" has been retained.', 'qode-wishlist-for-woocommerce' ), $wishlist['table_title'] );
						$response['items_retained'] = true;
					}
				}
				$response['done'] = 10 > count( $wishlists );
			} else {
				$response['done'] = true;
			}

			return $response;
		}
	}

	Qode_Wishlist_For_WooCommerce_Privacy_Wishlist::get_instance();
}
