<?php
/**
 * Main PHP file used to for initial calls to zeen101's Leak Paywall classes and functions.
 *
 * @package zeen101's Leak Paywall - Subscriber Meta
 * @since 1.0.0
 */
 
/*
Plugin Name: Leaky Paywall - Subscriber Meta
Plugin URI: http://zeen101.com/
Description: A premium addon for the Leaky Paywall for WordPress plugin.
Author: zeen101 Development Team
Version: 2.2.0
Author URI: http://zeen101.com/
Tags:
*/

//Define global variables...
if ( !defined( 'ZEEN101_STORE_URL' ) )
	define( 'ZEEN101_STORE_URL',	'http://zeen101.com' );
	
define( 'LP_SM_NAME', 		'Leaky Paywall - Subscriber Meta' );
define( 'LP_SM_SLUG', 		'issuem-leaky-paywall-subscriber-meta' );
define( 'LP_SM_VERSION', 	'2.2.0' );
define( 'LP_SM_DB_VERSION', '1.0.0' );
define( 'LP_SM_URL', 		plugin_dir_url( __FILE__ ) );
define( 'LP_SM_PATH', 		plugin_dir_path( __FILE__ ) );
define( 'LP_SM_BASENAME', 	plugin_basename( __FILE__ ) );
define( 'LP_SM_REL_DIR', 	dirname( LP_SM_BASENAME ) );

/**
 * Instantiate Pigeon Pack class, require helper files
 *
 * @since 1.0.0
 */
function issuem_leaky_paywall_subscriber_meta_plugins_loaded() {
	
	global $is_leaky_paywall, $which_leaky_paywall;
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'issuem/issuem.php' ) )
		define( 'ACTIVE_LP_SM', true );
	else
		define( 'ACTIVE_LP_SM', false );

	if ( is_plugin_active( 'issuem-leaky-paywall/issuem-leaky-paywall.php' ) ) {
		$is_leaky_paywall = true;
		$which_leaky_paywall = '_issuem';
	} else if ( is_plugin_active( 'leaky-paywall/leaky-paywall.php' ) ) {
		$is_leaky_paywall = true;
		$which_leaky_paywall = '';
	} else {
		$is_leaky_paywall = false;
		$which_leaky_paywall = '';
	}


	if ( !empty( $is_leaky_paywall ) ) {
		require_once( 'class.php' );
	
		// Instantiate the Pigeon Pack class
		if ( class_exists( 'Leaky_Paywall_Subscriber_Meta' ) ) {
			
			global $dl_pluginissuem_leaky_paywall_subscriber_meta;
			
			$dl_pluginissuem_leaky_paywall_subscriber_meta = new Leaky_Paywall_Subscriber_Meta();
			
			require_once( 'functions.php' );
				
			//Internationalization
			load_plugin_textdomain( 'issuem-lp-sm', false, LP_SM_REL_DIR . '/i18n/' );
				
		}
	
	} else {
	
		add_action( 'admin_notices', 'issuem_leaky_paywall_subscriber_meta_requirement_nag' );
		
	}

}
add_action( 'plugins_loaded', 'issuem_leaky_paywall_subscriber_meta_plugins_loaded', 4815162342 ); //wait for the plugins to be loaded before init

function issuem_leaky_paywall_subscriber_meta_requirement_nag() {
	?>
	<div id="leaky-paywall-requirement-nag" class="update-nag">
		<?php _e( 'You must have the Leaky Paywall plugin activated to use the Leaky Paywall Subscriber Meta plugin.' ); ?>
	</div>
	<?php
}
