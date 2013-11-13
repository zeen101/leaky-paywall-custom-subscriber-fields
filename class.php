<?php
/**
 * Registers IssueM's Leaky Paywall class
 *
 * @package IssueM's Leaky Paywall
 * @since 1.0.0
 */

/**
 * This class registers the main issuem functionality
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'IssueM_Leaky_Paywall_Subscriber_Meta' ) ) {
	
	class IssueM_Leaky_Paywall_Subscriber_Meta {
		
		private $plugin_name	= ISSUEM_LP_SM_NAME;
		private $plugin_slug	= ISSUEM_LP_SM_SLUG;
		private $basename		= ISSUEM_LP_SM_BASENAME;
		
		/**
		 * Class constructor, puts things in motion
		 *
		 * @since 1.0.0
		 */
		function __construct() {
					
			$settings = $this->get_settings();
			
			add_action( 'admin_init', array( $this, 'upgrade' ) );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_wp_enqueue_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'admin_wp_print_styles' ) );
			
			add_action( 'issuem_leaky_paywall_settings_form', array( $this, 'settings_div' ) );
			add_action( 'issuem_leaky_paywall_update_settings', array( $this, 'update_settings_div' ) );
			
			add_filter( 'leaky_paywall_subscribers_columns', array( $this, 'leaky_paywall_subscribers_columns' ) );
			//add_filter( 'leaky_paywall_subscribers_sortable_columns', array( $this, 'leaky_paywall_subscribers_sortable_columns' ) );
			add_filter( 'manage_leaky_paywall_susbcribers_custom_column', array( $this, 'manage_leaky_paywall_susbcribers_custom_column' ), 10, 3 );
			add_action( 'update_leaky_paywall_subscriber_form', array( $this, 'update_leaky_paywall_subscriber_form' ) );
			add_action( 'update_leaky_paywall_subscriber', array( $this, 'update_leaky_paywall_subscriber' ) );
			add_action( 'add_leaky_paywall_subscriber_form', array( $this, 'add_leaky_paywall_subscriber_form' ) );
			add_action( 'add_leaky_paywall_subscriber', array( $this, 'add_leaky_paywall_subscriber' ) );
			add_filter( 'issuem_leaky_paywall_subscriber_query_join', array( $this, 'issuem_leaky_paywall_subscriber_query_join' ) );
			add_filter( 'issuem_leaky_paywall_search_susbcriber_where_array', array( $this, 'issuem_leaky_paywall_search_susbcriber_where_array' ), 10, 3 );
			
		}
		
		/**
		 * Prints backend IssueM styles
		 *
		 * @since 1.0.0
		 */
		function admin_wp_print_styles() {
		
			global $hook_suffix;
			
			if ( 'leaky-paywall_page_leaky-paywall-subscribers' === $hook_suffix
				|| 'toplevel_page_issuem-leaky-paywall' === $hook_suffix )
				wp_enqueue_style( 'issuem_leaky_paywall_sm_settings_style', ISSUEM_LP_SM_URL . 'css/issuem-leaky-paywall-settings.css', '', ISSUEM_LP_SM_VERSION );
			
		}
	
		/**
		 * Enqueues backend IssueM styles
		 *
		 * @since 1.0.0
		 */
		function admin_wp_enqueue_scripts( $hook_suffix ) {
			
			if ( 'leaky-paywall_page_leaky-paywall-subscribers' === $hook_suffix
				|| 'toplevel_page_issuem-leaky-paywall' === $hook_suffix )
				wp_enqueue_script( 'issuem_leaky_paywall_sm_settings_js', ISSUEM_LP_SM_URL . 'js/issuem-leaky-paywall-settings.js', array( 'jquery' ), ISSUEM_LP_SM_VERSION );
				
			
		}
		
		/**
		 * Get IssueM's Leaky Paywall - Subscriber Meta options
		 *
		 * @since 1.0.0
		 */
		function get_settings() {
			
			$defaults = array( 
				'meta_keys' => array(),
			);
		
			$defaults = apply_filters( 'issuem_leaky_paywall_subscriber_meta_default_settings', $defaults );
			
			$settings = get_option( 'issuem-leaky-paywall-subscriber-meta' );
												
			return wp_parse_args( $settings, $defaults );
			
		}
		
		/**
		 * Update IssueM's Leaky Paywall options
		 *
		 * @since 1.0.0
		 */
		function update_settings( $settings ) {
			
			update_option( 'issuem-leaky-paywall-subscriber-meta', $settings );
			
		}
		
		/**
		 * Create and Display IssueM settings page
		 *
		 * @since 1.0.0
		 */
		function settings_div() {
			
			// Get the user options
			$settings = $this->get_settings();
			
			// Display HTML form for the options below
			?>
            <div id="modules" class="postbox">
            
                <div class="handlediv" title="Click to toggle"><br /></div>
                
                <h3 class="hndle"><span><?php _e( 'Leaky Paywall - Subscriber Meta', 'issuem-lp-sm' ); ?></span></h3>
                
                <div class="inside">
                
                <table id="issuem_leaky_paywall_subscriber_meta_wrapper">
                
                    <tr>
	                    <td><?php _e( 'Meta Key Name', 'issuem-lp-sm' ); ?></td>
						<td><?php _e( 'Show on Subscribers Page?', 'issuem-lp-sm' ); ?></td>
						<td><?php _e( 'Delete?', 'issuem-lp-sm' ); ?></td>
                    </tr>
                    
                    <?php
                    $count = 0;
                    if ( !empty( $settings['meta_keys'] ) ) {
	                    foreach ( $settings['meta_keys'] as $meta_key ) {
	                    $name = !empty( $meta_key['name'] ) ? $meta_key['name'] : '';
	                    $checked = !empty( $meta_key['checked'] ) ? $meta_key['checked'] : 'off';
	                    ?>
	                    <tr>
			                <td><input type="text" name="meta_keys[<?php echo $count; ?>][name]" value="<?php echo $name; ?>" /></td>
			                <td><input type="checkbox" name="meta_keys[<?php echo $count; ?>][checked]" value="on" <?php checked( 'on', $checked ); ?> /></td>
			                <td><a href="#" class="delete_lp_meta_key">x</a></td>
	                    </tr>
	                    <?php
	                    $count++;
	                    }
                    }
                    ?>
                    
                </table>
                
				<script type="text/javascript" charset="utf-8">
				var subscriber_meta_key_count = <?php echo $count; ?>;
				</script>
                                                           
                <p class="submit">
                    <input class="button button-small add_lp_meta_key" type="submit" name="add_lp_meta_key" value="<?php _e( 'Add Meta Key', 'issuem-lp-sm' ) ?>" />
                </p>
                                                              
                <p class="submit">
                    <input class="button-primary" type="submit" name="update_issuem_leaky_paywall_settings" value="<?php _e( 'Save Settings', 'issuem-lp-sm' ) ?>" />
                </p>

                </div>
                
            </div>
			<?php
			
		}
		
		function update_settings_div() {
		
			$settings = $this->get_settings();
				
			if ( !empty( $_REQUEST['meta_keys'] ) )
				$settings['meta_keys'] = $_REQUEST['meta_keys'];
			else
				$settings['meta_keys'] = '';
			
			$this->update_settings( $settings );
			
		}
		
		function leaky_paywall_subscribers_columns( $columns ) {
		
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
                		$label = $meta_key['name'];
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
                		$columns[$meta_key] = $label;
            		}
                }
            }
            
            return $columns;
		}
		
		function leaky_paywall_subscribers_sortable_columns( $columns ) {
		
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
                		$columns[$meta_key] = array( 'lpsm.meta_key', false );
            		}
                }
            }
            
            return $columns;
		}
		
		function manage_leaky_paywall_susbcribers_custom_column( $output, $column, $hash ) {
			
			return issuem_leaky_paywall_get_user_meta( $hash, $column );
			
		}
		
		function update_leaky_paywall_subscriber_form( $subscriber ) {
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
                		$label = $meta_key['name'];
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
	                ?>
                    	<p>
                        <label for="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" style="display:table-cell"><?php echo $label; ?></label>
                        <input id="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" class="subscriber-meta-key subscriber-<?php echo $meta_key; ?>-meta-key" type="text" value="<?php echo issuem_leaky_paywall_get_user_meta( $subscriber->hash, $meta_key ); ?>" name="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key"  />
                        </p>
	                <?php
                	}
                }
            }
		}
		
		function update_leaky_paywall_subscriber( $subscriber ) {
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['name'] ) ) {
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
	                	if ( !empty( $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] ) ) {
	                		issuem_leaky_paywall_update_user_meta( $subscriber->hash, $meta_key, $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] );
	                	}
                	}
                }
            }
		}
		
		function add_leaky_paywall_subscriber_form() {
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
                		$label = $meta_key['name'];
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
	                ?>
                    	<p>
                        <label for="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" style="display:table-cell"><?php echo $label; ?></label>
                        <input id="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" class="subscriber-meta-key subscriber-<?php echo $meta_key; ?>-meta-key" type="text" value="" name="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key"  />
                        </p>
	                <?php
                	}
                }
            }
		}
		
		function add_leaky_paywall_subscriber( $subscriber ) {
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['name'] ) ) {
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
	                	if ( !empty( $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] ) ) {
	                		issuem_leaky_paywall_update_user_meta( $subscriber->hash, $meta_key, $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] );
	                	}
                	}
                }
            }
		}
		
		function issuem_leaky_paywall_subscriber_query_join( $join ) {
			global $wpdb;
			return $join . ' LEFT JOIN ' . $wpdb->prefix . 'issuem_leaky_paywall_subscriber_meta AS lpsm ON lpsm.hash = lps.hash ';
		}
		
		function issuem_leaky_paywall_search_susbcriber_where_array( $where_array, $search_type, $search  ) {
			$where_array[] .= sprintf( "lpsm.`meta_value` %s '%s'", $search_type, $search );
            return $where_array;
		}
		
		/**
		 * Upgrade function, tests for upgrade version changes and performs necessary actions
		 *
		 * @since 1.0.0
		 */
		function upgrade() {
			
			$settings = $this->get_settings();
			
			if ( isset( $settings['version'] ) )
				$old_version = $settings['version'];
			else
				$old_version = 0;
				
			/* Table Version Changes */
			//if ( isset( $settings['db_version'] ) )
		//		$old_db_version = $settings['db_version'];
	//		else
				$old_db_version = 0;
			
			if ( version_compare( $old_db_version, '1.0.0', '<' ) )
				$this->init_db_table();

			$settings['version'] = ISSUEM_LP_SM_VERSION;
			$settings['db_version'] = ISSUEM_LP_SM_DB_VERSION;
			
			$this->update_settings( $settings );
			
		}
		
		function init_db_table() {
			
			global $wpdb;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			$table_name = $wpdb->prefix . 'issuem_leaky_paywall_subscriber_meta';

			$sql = "CREATE TABLE $table_name (
				hash        VARCHAR(64)   NOT NULL,
				meta_key    VARCHAR(254)  NOT NULL,
				meta_value  VARCHAR(254)  NOT NULL
			);";
			
			dbDelta( $sql );
			
		}
		
	}
	
}