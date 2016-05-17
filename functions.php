<?php
/**
 * @package zeen101's Leaky Paywall - Subscriber Meta
 * @since 1.0.0
 */

if ( !function_exists( 'wp_print_r' ) ) {

	/**
	 * Helper function used for printing out debug information
	 *
	 * HT: Glenn Ansley @ iThemes.com
	 *
	 * @since 1.0.0
	 *
	 * @param int $args Arguments to pass to print_r
	 * @param bool $die TRUE to die else FALSE (default TRUE)
	 */
    function wp_print_r( $args, $die = true ) {

        $echo = '<pre>' . print_r( $args, true ) . '</pre>';

        if ( $die ) die( $echo );
        	else echo $echo;

    }

}

function get_leaky_user_meta( $user_id, $key ){
	global $which_leaky_paywall;

	// Try for the new meta string first
	$meta = get_user_meta( $user_id, $which_leaky_paywall . $key, true );

	// If that returned nothing, try for an un-prefixed meta string
	if ( empty( $meta ) ){
		$meta = get_user_meta( $user_id, $key, true );
	}

	// Return whichever result returned, if any
	return $meta;

}
