<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

global $wpdb;

$slide_copy = array(
	1 => array(
		'kicker'   => 'Flavor & Freshness',
		'headline' => "Your Everyday Treat of Tasty Goodness.",
		'body'     => "From ancient authentic roots to modern wellness trends, dry fruits and mouth fresheners have always been part of India's rich culinary heritage.",
		'cta'      => 'Free delivery for orders above ₹2,500',
	),
	2 => array(
		'kicker'   => 'Premium Dry Fruits',
		'headline' => 'Handpicked Almonds, Cashews & More',
		'body'     => 'Bold flavours and wholesome crunch — roasted, seasoned, and crafted for everyday indulgence.',
		'cta'      => 'Shop Dry Fruits',
	),
	3 => array(
		'kicker'   => 'Crunchy Chips & Mixes',
		'headline' => 'Wholesome Snacking, Reimagined',
		'body'     => 'From masala chips to protein mixes — tasty goodness for every mood and moment.',
		'cta'      => 'Explore Mixes & Chips',
	),
	4 => array(
		'kicker'   => 'Mouth Freshners',
		'headline' => 'Traditional Paan & Refreshing Shots',
		'body'     => 'Authentic Indian mouth fresheners crafted with care — paan, goli, and fruity shots.',
		'cta'      => 'Discover Mouth Freshners',
	),
	5 => array(
		'kicker'   => 'Nutterly Good',
		'headline' => 'Your Everyday Treat of Tasty Goodness',
		'body'     => 'Premium quality dry fruits, brittles, chips and mixes — delivered across India.',
		'cta'      => 'Start Shopping Now',
	),
);

function ng_set_position_left( &$layer, $y_desktop, $y_mobile = null ) {
	$layer['position']['horizontal']['d']['v'] = 'left';
	$layer['position']['horizontal']['n']['v'] = 'left';
	$layer['position']['horizontal']['t']['v'] = 'left';
	$layer['position']['horizontal']['m']['v'] = 'left';
	$layer['position']['vertical']['d']['v']   = 'top';
	$layer['position']['vertical']['n']['v']   = 'top';
	$layer['position']['vertical']['t']['v']   = 'top';
	$layer['position']['vertical']['m']['v']   = 'top';
	$layer['position']['x']['d']['v']          = '80px';
	$layer['position']['x']['n']['v']          = '60px';
	$layer['position']['x']['t']['v']          = '40px';
	$layer['position']['x']['m']['v']          = '24px';
	$layer['position']['y']['d']['v']          = $y_desktop;
	$layer['position']['y']['n']['v']          = $y_mobile ?? $y_desktop;
	$layer['position']['y']['t']['v']          = $y_mobile ?? $y_desktop;
	$layer['position']['y']['m']['v']          = $y_mobile ?? $y_desktop;
}

function ng_text_style( &$layer, $size, $color, $line_height, $weight = '500', $font = 'Manrope', $max_width = null ) {
	$layer['idle']['fontFamily']              = $font;
	$layer['idle']['fontSize']['d']['v']      = $size;
	$layer['idle']['fontSize']['n']['v']      = $size;
	$layer['idle']['fontSize']['t']['v']      = max( 14, (int) $size - 2 ) . 'px';
	$layer['idle']['fontSize']['m']['v']      = max( 14, (int) $size - 8 ) . 'px';
	$layer['idle']['lineHeight']['d']['v']    = $line_height;
	$layer['idle']['lineHeight']['n']['v']    = $line_height;
	$layer['idle']['lineHeight']['t']['v']    = $line_height;
	$layer['idle']['lineHeight']['m']['v']    = ( (int) $line_height + 6 ) . 'px';
	$layer['idle']['fontWeight']['d']['v']    = $weight;
	$layer['idle']['fontWeight']['n']['v']    = $weight;
	$layer['idle']['fontWeight']['t']['v']    = $weight;
	$layer['idle']['fontWeight']['m']['v']    = $weight;
	$layer['idle']['color']['d']['v']         = $color;
	$layer['idle']['color']['n']['v']         = $color;
	$layer['idle']['color']['t']['v']         = $color;
	$layer['idle']['color']['m']['v']         = $color;
	$layer['idle']['textAlign']['d']['v']     = 'left';
	$layer['idle']['textAlign']['n']['v']     = 'left';
	$layer['idle']['textAlign']['t']['v']     = 'left';
	$layer['idle']['textAlign']['m']['v']     = 'left';
	if ( $max_width ) {
		$layer['size']['width']['d']['v'] = $max_width;
		$layer['size']['width']['n']['v'] = $max_width;
		$layer['size']['width']['t']['v'] = '90%';
		$layer['size']['width']['m']['v'] = '90%';
	}
}

function ng_clone_text_layer( $template, $uid, $alias, $text ) {
	$layer               = $template;
	$layer['uid']        = $uid;
	$layer['alias']      = $alias;
	$layer['text']       = $text;
	$layer['type']       = 'text';
	$layer['visibility'] = array(
		'd' => true,
		'n' => true,
		't' => true,
		'm' => true,
	);
	return $layer;
}

foreach ( $slide_copy as $order => $copy ) {
	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id, layers FROM {$wpdb->prefix}revslider_slides WHERE slide_order = %d",
			$order
		)
	);
	if ( ! $row ) {
		continue;
	}

	$layers = json_decode( $row->layers, true );
	if ( ! is_array( $layers ) ) {
		continue;
	}

	$template = $layers['4'] ?? $layers[4] ?? null;
	if ( ! $template ) {
		continue;
	}

	// Remove decorative demo image layers.
	unset( $layers['1'], $layers[1], $layers['3'], $layers[3] );

	// Kicker.
	if ( isset( $layers['0'] ) || isset( $layers[0] ) ) {
		$key = isset( $layers['0'] ) ? '0' : 0;
		$layers[ $key ]['text'] = $copy['kicker'];
		ng_set_position_left( $layers[ $key ], '200px', '140px' );
		ng_text_style( $layers[ $key ], '22px', '#B99531', '30px', '600' );
	}

	// Headline.
	$key = isset( $layers['4'] ) ? '4' : 4;
	$layers[ $key ]['text'] = $copy['headline'];
	ng_set_position_left( $layers[ $key ], '250px', '180px' );
	ng_text_style( $layers[ $key ], '48px', '#0C533D', '56px', '700', 'Marcellus', '640px' );

	// Body.
	$layers['5'] = ng_clone_text_layer( $template, 5, 'Text-5', $copy['body'] );
	ng_set_position_left( $layers['5'], '380px', '300px' );
	ng_text_style( $layers['5'], '16px', '#0C533D', '26px', '400', 'Manrope', '560px' );

	// CTA line.
	$layers['6'] = ng_clone_text_layer( $template, 6, 'Text-6', $copy['cta'] );
	ng_set_position_left( $layers['6'], '500px', '420px' );
	ng_text_style( $layers['6'], '20px', '#B99531', '28px', '600', 'Manrope', '560px' );

	$wpdb->update(
		$wpdb->prefix . 'revslider_slides',
		array( 'layers' => wp_json_encode( $layers ) ),
		array( 'id' => $row->id ),
		array( '%s' ),
		array( '%d' )
	);

	echo "Fixed slide $order text layout.\n";
}

$page_id = (int) get_option( 'page_on_front' );
delete_post_meta( $page_id, '_elementor_css' );
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}
wp_cache_flush();
echo "Done.\n";