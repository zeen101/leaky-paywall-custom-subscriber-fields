<?php
/**
 * @package IssueM's Leaky Paywall - Subscriber Meta
 * @since 1.0.0
 */
 
if ( !function_exists( 'issuem_leaky_paywall_get_user_meta' ) ) {
	
	function issuem_leaky_paywall_get_user_meta( $hash, $key ) {
		
		global $wpdb;
		
		return $wpdb->get_var( $wpdb->prepare( 
						"
						SELECT meta_value 
						FROM " . $wpdb->prefix . "issuem_leaky_paywall_subscriber_meta
						WHERE hash = '%s'
						AND meta_key = '%s'
						", 
						$hash,
						$key
					) 
				);
		
	}
	
}

if ( !function_exists( 'issuem_leaky_paywall_update_user_meta' ) ) {
	
	function issuem_leaky_paywall_update_user_meta( $hash, $key, $value ) {
		
		global $wpdb;
		
		$current_value = issuem_leaky_paywall_get_user_meta( $hash, $key );
				
		if ( !empty( $current_value ) ) {
			return $wpdb->update( 
				$wpdb->prefix . "issuem_leaky_paywall_subscriber_meta", 
				array( 
					'meta_value' => $value	// string 
				), 
				array( 
					'hash'       => $hash,
					'meta_key'   => $key,	// string 
				), 
				array( 
					'%s',	// meta_key
					'%s'	// meta_value
				), 
				array( '%s' ) 
			);
		} else if ( $current_value !== $value ) {
			return $wpdb->insert( 
				$wpdb->prefix . "issuem_leaky_paywall_subscriber_meta", 
				array( 
				    'hash'       => $hash, // string
					'meta_key'   => $key,	// string
					'meta_value' => $value	// string 
				), 
				array( 
					'%s',	// hash
					'%s',	// meta_key
					'%s'	// meta_value
				) 
			);
		}
		
		return false;
	}
}

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