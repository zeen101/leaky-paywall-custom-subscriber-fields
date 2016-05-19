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

/**
 * Get user meta, attempting with and without the `_issuem` prefix.
 *
 * There was a period where several plugins were setting user meta information
 * without the _issuem prefix due to a bad check. This function compensates
 * for this inconsistency by first attempting to grab user meta values with the
 * $which_leaky_paywall prefix and if none exists, trying for an unprefixed version.
 *
 * @see get_user_meta
 * @global string $which_leaky_paywall user meta prefix
 *
 * @param int $user_id User ID.
 * @param string $key Desired main key string without prefix.
 * @return string Meta value.
 */
if ( !function_exists( 'get_leaky_user_meta' ) ) {
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
}
