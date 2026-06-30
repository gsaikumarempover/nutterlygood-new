<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Compare_For_WooCommerce_Privacy_Compare' ) ) {
	class Qode_Compare_For_WooCommerce_Privacy_Compare {
		private $plugin_name;
		private static $instance;

		public function get_plugin_name() {
			return $this->plugin_name;
		}

		public function set_plugin_name( $plugin_name ) {
			$this->plugin_name = $plugin_name;
		}

		public function __construct() {
			$this->set_plugin_name( 'QODE Compare for WooCommerce' );

			// Let's initialize the privacy.
			add_filter( 'qode_compare_for_woocommerce_filter_privacy_policy_guide_content', array( $this, 'add_message_in_section' ), 10, 2 );

			// Set up compare data exporter.
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

			// Set up compare data eraser.
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Compare_For_WooCommerce_Privacy_Compare
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
						'<p>%1$s</p><ul><li>%2$s</li></ul><p>%3$s</p>',
						esc_html__( 'When you visit and interact with our website, we track:', 'qode-compare-for-woocommerce' ),
						esc_html__( 'Products you add to compare - this is done in order to present you and other visitors your favorite products, and to create targeted email campaigns.', 'qode-compare-for-woocommerce' ),
						esc_html__( 'We also use cookies to follow compare contents while you browse our website.', 'qode-compare-for-woocommerce' )
					);
					break;
				case 'has_access':
					$content = sprintf(
						'<p>%1$s</p><ul><li>%2$s</li></ul><p>%3$s</p>',
						esc_html__( 'Our team members have access to the information you provide us with.', 'qode-compare-for-woocommerce' ),
						esc_html__( 'For instance, site Administrators and Shop Managers can view the general compare info, like added products and addition dates', 'qode-compare-for-woocommerce' ),
						esc_html__( 'Our team has access to this type of data in order to offer you better deals and promotions for the products you are interested in.', 'qode-compare-for-woocommerce' )
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
				'exporter_friendly_name' => esc_html__( 'Customer Comparisons', 'qode-compare-for-woocommerce' ),
				'callback'               => array( $this, 'compare_data_exporter' ),
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
				'eraser_friendly_name' => esc_html__( 'Customer Comparisons', 'qode-compare-for-woocommerce' ),
				'callback'             => array( $this, 'compare_data_eraser' ),
			);

			return $erasers;
		}

		/**
		 * Export user comparisons (only available for authenticated users' compare)
		 *
		 * @param string $email_address Email of the users that requested export.
		 * @param int    $page Current page processed.
		 *
		 * @return array
		 */
		public function compare_data_exporter( $email_address, $page ) {
			$done           = true;
			$page           = (int) $page;
			$offset         = 10 * ( $page - 1 );
			$user           = get_user_by( 'email', $email_address );
			$data_to_export = array();

			if ( $user instanceof WP_User ) {
				$comparisons = qode_compare_for_woocommerce_get_privacy_user_compare_items(
					array(
						'limit'   => 10,
						'offset'  => $offset,
						'user_id' => $user->ID,
					)
				);

				if ( ! empty( $comparisons ) ) {
					foreach ( $comparisons as $compare_key => $compare ) {
						$data_to_export[] = array(
							'group_id'    => 'qcfw_compare',
							'group_label' => esc_html__( 'Comparisons', 'qode-compare-for-woocommerce' ),
							'item_id'     => 'qwfw-compare-' . $compare_key,
							'data'        => $this->get_compare_personal_data( $compare ),
						);
					}

					$done = 10 > count( $comparisons );
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Retrieves data to export for each user's compare
		 *
		 * @param array $compare
		 *
		 * @return array
		 */
		protected function get_compare_personal_data( $compare ) {
			$personal_data = array();

			$props_to_export = array(
				'token'        => esc_html__( 'Token', 'qode-compare-for-woocommerce' ),
				'date_created' => _x( 'Created on', 'date when compare was created', 'qode-compare-for-woocommerce' ),
				'items'        => esc_html__( 'Items added', 'qode-compare-for-woocommerce' ),
			);

			foreach ( $props_to_export as $prop => $name ) {
				$value = '';

				switch ( $prop ) {
					case 'items':
						$item_names = array();
						$items      = $compare['items'] ?? array();

						foreach ( $items as $item ) {
							$product = wc_get_product( $item['product_id'] );

							if ( ! $product ) {
								continue;
							}

							$item_name = sprintf(
								'%s %s %s %s',
								wp_kses_post( $product->get_name() ),
								esc_html__( 'x', 'qode-compare-for-woocommerce' ),
								esc_attr( $item['quantity'] ),
								// translators: date when item is added into compare.
								isset( $item['date_added'] ) ? sprintf( esc_html__( '(on: %s)', 'qode-compare-for-woocommerce' ), $item['date_added'] ) : ''
							);

							$item_names[] = $item_name;
						}

						$value = implode( ', ', $item_names );
						break;
					default:
						if ( isset( $compare[ $prop ] ) ) {
							$value = $compare[ $prop ];
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
		 * Deletes user comparisons (only available for authenticated users' compare)
		 *
		 * @param string $email_address Email of the users that requested export.
		 * @param int    $page Current page processed.
		 *
		 * @return array Result of the operation
		 */
		public function compare_data_eraser( $email_address, $page ) {
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

			$comparisons = qode_compare_for_woocommerce_get_privacy_user_compare_items(
				array(
					'limit'   => 10,
					'offset'  => $offset,
					'user_id' => $user->ID,
				)
			);

			if ( ! empty( $comparisons ) ) {
				foreach ( $comparisons as $compare ) {

					if ( apply_filters( 'qode_compare_for_woocommerce_filter_privacy_erase_compare_personal_data', true, $compare ) ) {
						qode_compare_for_woocommerce_remove_compare_item_db( $compare['token'], $compare['table_key'] );

						// translators: %s compare unique token.
						$response['messages'][]    = sprintf( esc_html__( 'Removed compare "%s".', 'qode-compare-for-woocommerce' ), $compare['table_title'] );
						$response['items_removed'] = true;
					} else {
						// translators: %s compare unique token.
						$response['messages'][]     = sprintf( esc_html__( 'Compare "%s" has been retained.', 'qode-compare-for-woocommerce' ), $compare['table_title'] );
						$response['items_retained'] = true;
					}
				}
				$response['done'] = 10 > count( $comparisons );
			} else {
				$response['done'] = true;
			}

			return $response;
		}
	}

	Qode_Compare_For_WooCommerce_Privacy_Compare::get_instance();
}
