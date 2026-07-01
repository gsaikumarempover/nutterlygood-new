<?php
/**
 * Nutterly Good — official contact & address (single source of truth).
 */

if ( ! function_exists( 'nuttergood_farmley_contact_info' ) ) {
	/**
	 * @return array<string, string>
	 */
	function nuttergood_farmley_contact_info() {
		return array(
			'company'       => 'Nutterly Good',
			'email'         => 'contact@nutterlygood.com',
			'phone'         => '+91 74162 85566',
			'phone_tel'     => '+917416285566',
			'address_line1' => 'CS-09, Etna Block, Rajapushpa Atria',
			'address_line2' => 'Golden Mile Road, Kokapet',
			'address_city'  => 'Hyderabad, Telangana 500075',
			'address'       => 'CS-09, Etna Block, Rajapushpa Atria, Golden Mile Road, Kokapet, Hyderabad, Telangana 500075',
			'packed_by'     => 'Nutterly Good, CS-09, Etna Block, Rajapushpa Atria, Golden Mile Road, Kokapet, Hyderabad, Telangana 500075',
			'map_lat'       => '17.3921',
			'map_lng'       => '78.3396',
			'map_url'       => 'https://www.google.com/maps/search/?api=1&query=Rajapushpa+Atria+Kokapet+Hyderabad',
			'hours'         => 'All Days: 11:00 AM – 9:00 PM IST',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_contact_phone_digits' ) ) {
	/**
	 * Digits-only phone for tel:/WhatsApp links.
	 */
	function nuttergood_farmley_contact_phone_digits() {
		$info = nuttergood_farmley_contact_info();
		$raw  = $info['phone_tel'] ?? $info['phone'] ?? '';

		return preg_replace( '/\D+/', '', (string) $raw );
	}
}

if ( ! function_exists( 'nuttergood_farmley_contact_whatsapp_url' ) ) {
	/**
	 * WhatsApp chat link — same number as phone.
	 *
	 * @param string $message Optional pre-filled message.
	 */
	function nuttergood_farmley_contact_whatsapp_url( $message = '' ) {
		$digits = nuttergood_farmley_contact_phone_digits();
		if ( '' === $digits ) {
			return '';
		}

		$url = 'https://wa.me/' . $digits;
		if ( '' !== $message ) {
			$url .= '?text=' . rawurlencode( $message );
		}

		return $url;
	}
}

if ( ! function_exists( 'nuttergood_farmley_contact_address_html' ) ) {
	/**
	 * Multi-line address for display blocks.
	 */
	function nuttergood_farmley_contact_address_html() {
		$info = nuttergood_farmley_contact_info();
		return $info['company'] . '<br />'
			. $info['address_line1'] . '<br />'
			. $info['address_line2'] . '<br />'
			. $info['address_city'];
	}
}